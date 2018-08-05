<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 05/08/2018
 * Time: 13:27
 */

namespace App\EGWK\Parser;


use App\EGWK\Parser;

abstract class Reference extends Parser
{

    /**
     * @return mixed
     */
    public function getReferences()
    {
        return $this->parsed;
    }
}
