<?php

namespace App\Models\Tables;

class SimilarParagraphReversed extends SimilarParagraph
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

}
