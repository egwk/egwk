<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 14/08/2018
 * Time: 16:30
 */

namespace App\EGWK\Datamining;

use App\EGWK\Datamining;
use Foolz\SphinxQL\SphinxQL;
use Illuminate\Support\Facades\DB;
use App\EGWK\Tools\Bench;

class ParagraphSimilarity extends Datamining
{
    protected function query($start = 0, $limit = 0, $offset = 0)
    {
        $paragraphsQuery = DB::table($this->table)
            ->select('id', 'para_id', 'stemmed_wordlist');
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

    protected function search($connection, $paragraph)
    {
        $stemmedWordlist = '"' . $paragraph->stemmed_wordlist . '"/' . $this->quorumPercentage;
        $queryResult = SphinxQL::create($connection)
            ->select('id', 'para_id AS para_id1', 'stemmed_wordlist', SphinxQL::expr('WEIGHT()/100 AS w1'))
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
        foreach ($results->fetchAllAssoc() as $result) {
            $resultObject = (object)$result;
            if ($this->skipCondition($resultObject)) {
                continue;
            }
            $weight2 = $this->weightBackward($paragraph->stemmed_wordlist, $resultObject->stemmed_wordlist);
            if ($paragraph->id > $resultObject->id && $weight2 >= $this->quorumPercentage * 100) {
                continue;
            }
            $result = collect($result);
//	    $processed = collect(['para_id1' => $paragraph->para_id, 'content1' => $paragraph->stemmed_wordlist, 'w2' => $weight2])
//		    ->merge($result->except(['id']));
            $processed = collect(['para_id2' => $paragraph->para_id, 'w2' => sprintf("%.2f", $weight2),])//'id1' => $paragraph->id,
            ->merge($result->except(['id', 'stemmed_wordlist']))
                ->toArray();
            $processed['w1'] = sprintf("%.2f", $processed['w1']);
            $return->push(collect($processed)->implode(','));
        }
        return $return->implode("\n");
    }



}
