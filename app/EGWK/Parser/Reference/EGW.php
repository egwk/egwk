<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 05/08/2018
 * Time: 13:27
 */

namespace App\EGWK\Parser\Reference;


use App\EGWK\Parser\Reference;
use App\Models\Tables\CacheSearch;

class EGW extends Reference
{

    public function parse()
    {
        preg_match_all('/\{(.+?)\}/', $this->data, $tmp);
        $this->parsed = array_pop($tmp);
        return $this;
    }

    public function getParagraphs()
    {
        foreach ($this->parsed as $ref) {
            if ($hit = CacheSearch::search($ref)
                ->get()
                ->first()) {
                yield [$ref, html_entity_decode(strip_tags($hit->content), ENT_QUOTES | ENT_HTML5)];
            }
        }
    }

    public function getCSVParagraphs()
    {
        $csv = "";
        foreach ($this->getParagraphs() as $paragraph) {
            $csv .= implode("\t", $paragraph) . "\n";
        }
        return $csv;
    }

    public function filter()
    {
        // TODO: Implement filter() method.
    }

    public function tokenize()
    {
        // TODO: Implement tokenize() method.
    }
}
