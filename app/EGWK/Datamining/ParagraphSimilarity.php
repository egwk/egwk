<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 14/08/2018
 * Time: 16:30
 */

namespace App\EGWK\Datamining;

use App\EGWK\Datamining;
use App\EGWK\Reader\SearchSimilar;
use Foolz\SphinxQL\SphinxQL;
use Illuminate\Support\Facades\DB;
use App\EGWK\Tools\Bench;

class ParagraphSimilarity extends Datamining
{
    /**
     * @inheritdoc
     */
    protected $ranker = "expr('sum(hit_count)*10000/query_word_count')";

    protected $searchSimilar;

    protected $similarsAlready;

    public function __construct(StorageDriver $storage)
    {
        parent::__construct($storage);
        $this->searchSimilar = new SearchSimilar();
    }

    protected function query($start = 0, $limit = 0, $offset = 0)
    {
        $paragraphsQuery = DB::table($this->table)
            ->select('id', 'para_id', 'stemmed_wordlist');
        // $paragraphsQuery->whereIn('year', ['1952', '1892'])->whereIn('refcode_1', ['ML', 'RH']); // todo: for testing
        if (0 != $start) {
            if ($this->isParaId($start)) {
                $startParagraph = $this->getParagraph($start);
                $start = $startParagraph->id;
            }
            $paragraphsQuery->where('id', '>=', $start);
        }
        if (0 != $limit) {
            if ($this->isParaId($limit)) {
                $stopParagraph = $this->getParagraph($limit);
                $stop = $stopParagraph->id;
                $limit = $stop - $start;
            }
            $paragraphsQuery->limit($limit);
        }
        if (0 != $offset) {
            $paragraphsQuery->offset($offset);
        }

        $count = $paragraphsQuery->count();
        $total = ($limit > 0 && $limit < $count) ? $limit : ($count > 0 ? $count : $limit);
        Bench::setTotal($total);
        return $paragraphsQuery->get();
    }

    protected function search($sphinx, $paragraph)
    {
        $stemmedWordlist = '"' . $paragraph->stemmed_wordlist . '"/' . $this->quorumPercentage;
        $queryResult = $sphinx
            ->select('id', 'para_id', 'stemmed_wordlist', SphinxQL::expr('WEIGHT()/100 AS w'))
            ->from($this->index)
            ->option('ranker', SphinxQL::expr($this->ranker))
            ->match('search_subject', SphinxQL::expr($stemmedWordlist))
            ->where('id', '<>', $paragraph->id)
            ->limit(30000)
            ->execute();
        return $queryResult;
    }

    protected function skipCondition($paragraph)
    {
        return substr_count($paragraph->stemmed_wordlist, " ") < $this->minWordCount;
    }

    protected function specCondition($paragraph)
    {
        return substr_count($paragraph->stemmed_wordlist, " ") >= 254;
    }

    protected function weightBackward($s1, $s2)
    {
        $a1 = explode(' ', $s1);
        $a2 = explode(' ', $s2);
        $c2 = count($a2);
        $a12 = array_intersect($a1, $a2);
        $c12 = count($a12);
        return (100 * $c12 / $c2);
    }

    protected function processResult($results, $paragraph)
    {
        $return = collect([]);
        $this->similarsAlready = $this->searchSimilar
            ->similarParagraph($paragraph->para_id)
            ->pluck('para_id2')
            ->flatten();
        foreach ($results->fetchAllAssoc() as $resultArray) {
            $result = (object)$resultArray;
            if ($this->skipCondition($result) ||
                $this->similarsAlready->contains($result->para_id)) {
                continue;
            }
            if ($result->w < $this->quorumPercentage * 100) {
                continue;
            }
            $w2 = $this->weightBackward($paragraph->stemmed_wordlist, $result->stemmed_wordlist);
            $return->push(collect([
                'para_id1' => $result->para_id,
                'para_id2' => $paragraph->para_id,
                'w1' => sprintf("%.2f", $result->w),
                'w2' => sprintf("%.2f", $w2),
            ]));
        }
        return $return;
    }
}
