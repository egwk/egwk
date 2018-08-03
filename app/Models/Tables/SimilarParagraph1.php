<?php

namespace App\Models\Tables;

class SimilarParagraph1 extends SimilarParagraph
{

    public function searchableAs()
    {
        return env('SCOUT_PREFIX', 'e3si_') . 'similarity1';
    }

}
