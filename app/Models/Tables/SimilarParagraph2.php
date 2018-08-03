<?php

namespace App\Models\Tables;

class SimilarParagraph2 extends SimilarParagraph
{

    public function getParaId1Attribute()
    {
        return $this->attributes['para_id2'];
    }

    public function getParaId2Attribute()
    {
        return $this->attributes['para_id1'];
    }

    public function getW2Attribute()
    {
        return $this->attributes['w1'];
    }

    public function getW1Attribute()
    {
        return $this->attributes['w2'];
    }

    public function searchableAs()
    {
        return env('SCOUT_PREFIX', 'e3si_') . 'similarity2';
    }

}
