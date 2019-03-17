<?php

namespace App\Http\Controllers;

use App\EGWK\Devotional;
use App\EGWK\Devotional\Driver;
use Illuminate\Http\Request;

class DevotionalController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function all(string $id)
    {
        return Devotional::factory($id)
            ->all();
    }

    public function year(string $id, string $year)
    {
        return Devotional::factory($id)
            ->year($year);
    }

    public function today(string $id)
    {
        return Devotional::factory($id)
            ->today();
    }

    public function date(string $id, string $date)
    {
        return Devotional::factory($id)
            ->date($date);
    }
}
