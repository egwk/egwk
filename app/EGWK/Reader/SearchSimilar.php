<?php

namespace App\EGWK\Reader;


use App\Models\Tables\Publisher;
use App\Models\Tables\Translation;
use App\Models\Tables\CacheSearch,
    App\Models\Tables\SimilarParagraph,
    App\Models\Tables\SimilarParagraph1,
    App\Models\Tables\SimilarParagraph2;
use App\Models\Tables\Original;

class SearchSimilar
{

    const DEFAULT_THRESHOLD = 30;

    public function cluster($query, $thresholdCovers = null, $thresholdCovered = null, $referenceParaId = null, $lang = null)
    {
        $thresholdCovers = $thresholdCovers ?: static::DEFAULT_THRESHOLD;
        $thresholdCovered = $thresholdCovered ?: static::DEFAULT_THRESHOLD;

        $cluster = [];
        $index = 0;
        $result = $this->getSearchResult($query);
        $translations = collect([]);
        if ($lang !== null) {
            $publishers = Publisher::all()->keyBy('code');
            $translations = Translation::whereIn('para_id', $result->pluck('para_id'))
                ->where('lang', $lang)
                ->get()
                ->map(function ($item) use ($publishers) {
                    $po = $publishers->get($item->publisher);
                    $item->publisher = $po ? $po->name : $item->publisher;
                    // print_r($publishers->get($item->publisher, $item->publisher));
                    return $item;
                })
                ->groupBy('para_id');
            $result->map(function ($item) use ($translations) {
                $item->translations = $translations->get($item->para_id, []);
                return $item;
            });
        }
        do {
            $cluster[$index]['self'] = $this->getFirstRecord($result, $referenceParaId);
            if ($result->count() > 0) {
                foreach ($this->getSimilarParagraphs($result, $cluster[$index]['self']) as $similarItem) {
                    if ($similarItem->w2 >= $thresholdCovers && $similarItem->w1 >= $thresholdCovered) {
                        $similarRecord = $this->buildSimilarRecord($result, $similarItem);
                        if ($similarRecord !== null) {
                            $cluster[$index]['similars'][] = $similarRecord;
                            $result = $result->filter(function ($resultItem, $key) use ($similarItem) {
                                return $resultItem->para_id != $similarItem->para_id2;
                            });
                        }
                    }
                }
            }
            $index++;
        } while (!$result->isEmpty());
        return collect($cluster);
    }

    protected function buildSimilarRecord($result, $similarItem)
    {
        $paragraph = $result->filter(function ($resultItem, $key) use ($similarItem) {
            return $resultItem->para_id == $similarItem->para_id2;
        })->first();
        if ($paragraph === null) {
            return null;
        }
        return [
            'paragraph' => $paragraph,
            'similarity' => [
                'covered' => $similarItem->w1,
                'covers' => $similarItem->w2,
            ],
        ];
    }

    protected function getSimilarParagraphs($result, $first)
    {
        return collect($this->similarParagraph($first->para_id))
            ->whereIn('para_id2', $result->pluck('para_id'))
            ->sortBy('year');
    }

    protected function getSearchResult(string $query)
    {
        return collect(CacheSearch::search(\Reader::quotedPhraseQuery($query))
            ->take(config('egwk.api.query_limit', 1000))
            ->orderBy('year', 'asc')
            ->get())
            ->map(function ($item) {
                $item->content = trim(html_entity_decode(strip_tags($item->content)));
                return $item;
            });
    }

    protected function getFirstRecord(&$result, &$referencedParaId)
    {
        $first = null;
        if (null == $referencedParaId) {
            $first = $result->shift();
        } else {
            $first = $result->firstWhere('para_id', $referencedParaId);
            $result = $result->reject(function ($item, $key) use ($referencedParaId) {
                return $item->para_id == $referencedParaId;
            });
            $referencedParaId = null;
        }
        return $first;
    }

    public function similarParagraph($paraID)
    {
        $paraIDexpr = \Foolz\SphinxQL\SphinxQL::expr('="' . trim($paraID) . '"'); // Also: '="^' . trim($paraID) . '$";mode=extended'
        return collect(SimilarParagraph1::search($paraIDexpr)->get()->merge(SimilarParagraph2::search($paraIDexpr)->get()));
    }

    public function similarParagraphWithContent($paraID, $threshold)
    {
        $tmp = [];
        foreach ($this->similarParagraph($paraID) as $paragraph) {
            $covers = $paragraph->w2;
            if ($threshold <= $covers) {
                $similarParagraph = Original::where('para_id', $paragraph->para_id2)->first();
                if (!empty($similarParagraph)) {
                    $tmp[] = [
                        'paragraph' => $similarParagraph,
                        'covered' => $paragraph->w1,
                        'covers' => $covers,
                    ];
                }
            }
        }
        return collect($tmp)->sortBy('covered', SORT_REGULAR, true);
    }

    public function similarParagraphStandard($paraID)
    {
        $paraIDexpr = \Foolz\SphinxQL\SphinxQL::expr('="' . trim($paraID) . '"');
        return SimilarParagraph::search($paraIDexpr);
    }
}
