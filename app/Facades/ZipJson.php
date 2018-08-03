<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Description of Reader
 *
 * @author Peter
 */
class ZipJson extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \App\Tools\ZipJson::class;
    }

}
