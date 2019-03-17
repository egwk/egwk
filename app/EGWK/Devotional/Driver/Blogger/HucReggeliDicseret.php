<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 17/03/2019
 * Time: 17:40
 */

namespace App\EGWK\Devotional\Driver\Blogger;


use App\EGWK\Devotional\Driver\Blogger;

class HucReggeliDicseret extends Blogger
{
    public function __construct(string $id = 'hu-reggelidicseret', string $appName = '')
    {
        parent::__construct($id, $appName);
    }
}
