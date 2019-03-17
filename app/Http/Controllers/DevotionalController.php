<?php

namespace App\Http\Controllers;

use App\EGWK\Devotional;
use App\EGWK\DevotionalException;

class DevotionalController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function all(string $id)
    {
        try {
            return Devotional::factory($id)
                ->all();
        } catch (DevotionalException $e) {
            return ['error' => true, 'message' => 'Unknown devotional: ' . $id];
        }
    }

    public function year(string $id, string $year)
    {
        try {
            return Devotional::factory($id)
                ->year($year);
        } catch (DevotionalException $e) {
            return ['error' => true, 'message' => 'Unknown devotional: ' . $id];
        }
    }

    public function today(string $id)
    {
        try {
            return Devotional::factory($id)
                ->today();
        } catch (DevotionalException $e) {
            return ['error' => true, 'message' => 'Unknown devotional: ' . $id];
        }
    }

    public function date(string $id, string $date)
    {
        try {
            return Devotional::factory($id)
                ->date($date);
        } catch (DevotionalException $e) {
            return ['error' => true, 'message' => 'Unknown devotional: ' . $id];
        }
    }
}
