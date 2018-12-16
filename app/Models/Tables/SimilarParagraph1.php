<?php

namespace App\Models\Tables;

class SimilarParagraph1 extends SimilarParagraph
{

    public function searchableAs()
    {
        return config('scout.prefix', 'e3si_') . 'similarity1';
    }

}
