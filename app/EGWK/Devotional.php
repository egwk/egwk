<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 17/03/2019
 * Time: 17:36
 */

namespace App\EGWK;

use App\EGWK\Devotional\Driver;

class DevotionalException extends \Exception
{
}

class Devotional
{
    /**
     * @param string $id
     * @param string $appName
     * @return Driver
     * @throws \Exception
     */
    public static function factory(string $id, string $appName = '')
    {
        $class = config("EGWK.devotional.$id.class");
        if (class_exists($class)) {
            return new $class($id, $appName);
        } else {
            throw new DevotionalException('Class not found: ' . $class);
        }
    }
}
