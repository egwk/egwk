<?php

namespace App\Facades\ChurchFields\HUC;
 
use Illuminate\Support\Facades\Facade;

class SabbathSchool extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \App\EGWK\ChurchFields\HUC\SabbathSchool::class;
    }

}
 
