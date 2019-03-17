<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 17/03/2019
 * Time: 17:40
 */

namespace App\EGWK\ChurchFields\HUC;


use App\EGWK\Devotional\Driver\Blogger;

class Devotional extends Blogger
{
    public function __construct(string $id = 'huc-reggelidicseret', string $appName = '')
    {
        parent::__construct($id, $appName);
    }
}
