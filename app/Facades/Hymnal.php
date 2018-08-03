<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Description of Hymnal
 *
 * @author Peter
 */
class Hymnal extends Facade
    {

    protected static function getFacadeAccessor()
        {
        return \App\EGWK\Hymnal::class;
        }

    }
