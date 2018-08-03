<?php

namespace App\Facades\Reader;

use Illuminate\Support\Facades\Facade;

/**
 * Description of SearchSimilar
 *
 * @author Peter
 */
class SearchSimilar extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \App\EGWK\Reader\SearchSimilar::class;
    }

}
