<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Description of Reader
 *
 * @author Peter
 */
class Reader extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \App\EGWK\Reader::class;
    }

}
