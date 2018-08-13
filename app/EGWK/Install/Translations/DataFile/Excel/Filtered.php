<?php

namespace App\EGWK\Install\Translations\DataFile\Excel;

use App\EGWK\Install\Translations\DataFile\Excel;

/**
 * Excel
 *
 * @author Peter
 */
class Filtered extends Excel
{

    /**
     * Filter Original
     * 
     * @param string $original Original
     * @return mixed Filtered data
     */
    protected function filterOriginal($original)
    {
        $parsed = explode('×', $original, 4);
        if (count($parsed) == 3)
        {
            $parsed[] = "";
        }
        return $parsed;
    }

    /**
     * Filter Translation
     * 
     * @param string $translation Translation
     * @return mixed Filtered data
     */
    protected function filterTranslation($translation)
    {
        $filtered = preg_replace(['/\ \ +/', '/^#CHAPTER:\s*/', '/^#SECTION:\s*/', '/¤n¤/'], [' ', '', '', "\n"], trim($translation));
        return $filtered;
    }

}
