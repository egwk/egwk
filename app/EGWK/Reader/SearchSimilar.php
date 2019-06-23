<?php

namespace App\EGWK\Reader;


use App\Models\Tables\Publisher;
use App\Models\Tables\Translation;
use App\Models\Tables\CacheSearch,
    App\Models\Tables\SimilarParagraph,
    App\Models\Tables\SimilarParagraph1,
    App\Models\Tables\SimilarParagraph2;
use App\Models\Tables\Original;
use Illuminate\Support\Collection;

class SearchSimilar
{

    const DEFAULT_THRESHOLD = 30;
    protected $publishers = null;

    public function cluster($query, $thresholdCovers = null, $thresholdCovered = null, $referenceParaId = null, $lang = null): Collection
    {
        $thresholdCovers = $thresholdCovers ?: static::DEFAULT_THRESHOLD;
        $thresholdCovered = $thresholdCovered ?: static::DEFAULT_THRESHOLD;

        $cluster = [];
        $index = 0;
        $result = $this->getSearchResults($query, $lang);
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

    protected function getSearchResults($query, $lang = null, $quoted = true)
    {
        $result = $this->getOriginalSearchResult($query, $quoted);
        if ($result->count() > 0) {
            if ($lang !== null) {

                $translations = $this->getTranslationsByParaId($result->pluck('para_id'), $lang);
                $result->map(function ($item) use ($translations) {
                    $item->translations = $translations->get($item->para_id, []);
                    return $item;
                });
            }
        } else {
            $translations = $this->getTranslationSearchResult($query, $lang, $quoted);
            $result = $this->getOriginalsByParaId($translations->pluck('para_id'));
            $translationsGrouped = $translations->groupBy('para_id');
            $result->map(function ($item) use ($translationsGrouped) {
                $item->translations = $translationsGrouped->get($item->para_id, []);
                return $item;
            });
        }
        return $result;
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

    protected function getOriginalsByParaId($paraIdList)
    {
        return collect(CacheSearch::whereIn('para_id', $paraIdList)
            ->get())
            ->map(function ($item) {
                $item->content = trim(html_entity_decode(strip_tags($item->content)));
                return $item;
            });
    }

    protected function getTranslationsByParaId($paraIdList, $lang = null)
    {
        $publishers = $this->getPublishers();
        $tr = Translation::whereIn('para_id', $paraIdList);
        if ($lang !== null) {
            $tr->where('lang', $lang);
        }
        return $tr->get()
            ->map(function ($item) use ($publishers) {
                $publisher = $publishers->get($item->publisher);
                $item->publisher_name = $publisher ? $publisher->name : $item->publisher;
                return $item;
            })
            ->groupBy('para_id');
    }

    protected function getTranslationSearchResult(string $query, $lang = null, $quoted = true)
    {
        $publishers = $this->getPublishers();
        $query = $quoted ? \Reader::quotedPhraseQuery($query) : $query;
        $q = Translation::search($query)
            ->take(config('egwk.api.query_limit', 1000))
            ->orderBy('year', 'asc');
        if ($lang !== null) {
            $q->where('lang', $lang);
        }
        return collect($q->get())
            ->map(function ($item) use ($publishers) {
                $publisher = $publishers->get($item->publisher);
                $item->publisher = $publisher ? $publisher->name : $item->publisher;
                $item->content = trim(html_entity_decode(strip_tags($item->content)));
                return $item;
            });
       // return $c;
    }

    protected function getOriginalSearchResult(string $query, $quoted = true)
    {
        $query = $quoted ? \Reader::quotedPhraseQuery($query) : $query;
        return collect(CacheSearch::search($query)
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

    protected function getPublishers()
    {
        if (null === $this->publishers) {
            $this->publishers = Publisher::all()
                ->keyBy('code');
        }
        return $this->publishers;
    }

    public function similarParagraph($paraID): Collection
    {
        $paraIDexpr =  \Reader::quotedPhraseQuery(trim($paraID));// SphinxQL::expr('="' . trim($paraID) . '"') Also:  '="^' . trim($paraID) . '$";mode=extended'
        return collect(SimilarParagraph1::search($paraIDexpr)->get()->merge(SimilarParagraph2::search($paraIDexpr)->get()));
    }

    public function similarParagraphWithContent($paraID, $threshold): Collection
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
        return SimilarParagraph::search(\Reader::quotedPhraseQuery(trim($paraID)));
    }
}
