<?php

namespace App\EGWK\Tools;

/**
 * CSV trait
 * 
 * Adds method to create CSV
 *
 * @author Peter
 */
trait Csv
{

    /**
     * Splits text into sentences
     *
     * @access private
     * @param string $glue Glue
     * @return string Glue
     */
    private function createCsv($glue = ',', $eol = "\n")
    {
        $data = [];
        for ($i = 2; $i < func_num_args(); $i++)
        {
            $arg = func_get_arg($i);
            if (is_object($arg))
            {
                $arg = (array) ($arg);
            } elseif (!is_array($arg))
            {
                $arg = [$arg];
            }
            $data = array_filter(array_merge($data, $arg), function($e)
            {
                return !is_array($e);
            });
        }
        return implode($glue, $data) . $eol;
    }

}
