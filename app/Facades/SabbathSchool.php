<?php

namespace App\Facades;
 
use Illuminate\Support\Facades\Facade;

class SabbathSchool extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \App\EGWK\SabbathSchool::class;
    }

}
 
