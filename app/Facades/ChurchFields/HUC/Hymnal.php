<?php

namespace App\Facades\ChurchFields\HUC;

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
        return \App\EGWK\ChurchFields\HUC\Hymnal::class;
        }

    }
