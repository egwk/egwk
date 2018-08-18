<?php

namespace App\EGWK\Tools;

/**
 * Process Log trait
 * 
 * Logs task progress to screen
 *
 * @author Peter
 */
trait ProcessLog
{

    protected $logGlue = " :: ";
    protected $logLevelPad = "  ";
    protected $logBr = "\n";
    protected $logTick = ".";

    /**
     * Logs process to screen
     *
     * @access private
     * @param array $data Data to be shown in the log
     * @param int $level Log depth level
     * @param string $glue Glue to connect log elements
     * @param string $pad String for padding depth level
     * @return void
     */
    private function logProc(array $data, $level = 0, $glue = null, $pad = null)
    {
        $glue = $glue !== null ? $glue : $this->logGlue;
        $pad = $pad !== null ? $pad : $this->logLevelPad;
        echo str_repeat($pad, $level) . implode($glue, $data);
        return $this->logBr();
    }

    /**
     * Logs a single operation tick to screen
     *
     * @access private
     * @param string $tick String signifying a single operation tick (if not using default)
     * @return void
     */
    private function logTick($tick = null)
    {
        echo $tick !== null ? $tick : $this->logTick;
        return $this;
    }

    /**
     * Logs a line break to screen
     *
     * @access private
     * @param string $br Line break (if not using default)
     * @return void
     */
    private function logBr($br = null)
    {
        echo $br !== null ? $br : $this->logBr;
        return $this;
    }

}
