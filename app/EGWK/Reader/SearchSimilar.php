<?php

namespace App\EGWK\Reader;


use App\Models\Tables\CacheSearch,
    App\Models\Tables\SimilarParagraph,
    App\Models\Tables\SimilarParagraph1,
    App\Models\Tables\SimilarParagraph2;
use App\Models\Tables\Original;

class SearchSimilar
{

    const DEFAULT_THRESHOLD = 30;

    public function original($query, $thresholdCovers = null, $thresholdCovered = null, $referenceParaId = null)
    {
        $thresholdCovers = $thresholdCovers ?: static::DEFAULT_THRESHOLD;
        $thresholdCovered = $thresholdCovered ?: static::DEFAULT_THRESHOLD;

        $merged = [];
        $index = 0;
        $result = $this->getSearchResult($query);

        do {
            $merged[$index]['self'] = $this->getFirstRecord($result, $referenceParaId);
            if ($result->count() > 0) {
                foreach ($this->getSimilarParagraphs($result, $merged[$index]['self']) as $similarItem) {
                    if ($similarItem->w2 >= $thresholdCovers && $similarItem->w1 >= $thresholdCovered) {
                        $merged[$index]['similars'][] = $this->buildSimilarRecord($result, $similarItem);
                        $result = $result->filter(function ($resultItem, $key) use ($similarItem) {
                            return $resultItem->para_id != $similarItem->para_id2;
                        });
                    }
                }
            }
            $index++;
        } while (!$result->isEmpty());
        return collect($merged);
    }

    protected function buildSimilarRecord($result, $similarItem)
    {
        return [
            'paragraph' => $result->filter(function ($resultItem, $key) use ($similarItem) {
                return $resultItem->para_id == $similarItem->para_id2;
            })->first(),
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
