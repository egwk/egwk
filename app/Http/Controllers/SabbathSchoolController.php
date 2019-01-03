<?php

namespace App\Http\Controllers;

use App\Facades\SabbathSchool;
use Illuminate\Http\Request;

class SabbathSchoolController extends Controller
{

    public function list(Request $request)
    {
        return SabbathSchool::getList();
    }

    public function date($date)
    {
        return SabbathSchool::getByDate($date);
    }

    public function html($date)
    {
        return view('sabbathschool::html', array_merge(SabbathSchool::getByDate($date), ['title' => $date]));
    }

    public function quarter($year, $quarter = 1)
    {
        return SabbathSchool::getQuarter($year, $quarter);
    }

    public function weeks($year, $quarter = 1)
    {
        return SabbathSchool::getNoWeeks($year, $quarter);
    }

    public function week($year, $quarter, $weekNo)
    {
        ///todo: not working
        return SabbathSchool::getContentByWeekNo($year, $quarter, $weekNo);
    }

}
