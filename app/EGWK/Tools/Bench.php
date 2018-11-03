<?php

namespace App\EGWK\Tools;

class Bench
    {

    protected static $start;
    protected static $now;
    protected static $total;
    protected static $counter;
    protected static $elapsedTime;
    protected static $averageTime;
    protected static $progress;
    protected static $percentage;
    protected static $estimatedTotalTime;
    protected static $estimatedCompletionIn;
    protected static $estimatedCompletionTime;

    public static function start($echo = false)
	{
	static::$start = microtime(true);
	static::$now = 0;
	static::$counter = 0;
	static::$elapsedTime = 0;
	static::$total = 0;
	static::$averageTime = 0;
	static::$progress = 0;
	static::$percentage = 0;
	static::$estimatedTotalTime = 0;
	static::$estimatedCompletionIn = 0;
	static::$estimatedCompletionTime = 0;
	if ($echo)
	    {
	    echo "\n\nStarted at " . date('Y-m-d H:i:s', static::$start) . "\n\n";
	    }
	}

    public static function stop($echo = false)
	{
	if ($echo)
	    {
	    echo "\n\nFinished at " . date('Y-m-d H:i:s') . "\n";
	    echo "Total: " . static::formatTimeInterval(static::$elapsedTime) . "\n\n";
	    }
	}

    public static function formatTimeInterval($elapsedTimeInSeconds)
	{
	$space = "";
	$names = array("s", "m", "h", "days", "months", "years");
//	$names = array("sec", "min", "hrs", "days", "months", "years");
//	$names = array("seconds", "minutes", "hours", "days", "months", "years");
	$values = array(1, 60, 3600, 24 * 3600, 30 * 24 * 3600, 365 * 24 * 3600);

	for ($i = count($values) - 1; $i > 0 && $elapsedTimeInSeconds < $values[$i]; $i--)
	    ;
	if ($i == 0)
	    {
	    return intval($elapsedTimeInSeconds / $values[$i]) . $space . $names[$i];
	    }
	else
	    {
	    $t1 = intval($elapsedTimeInSeconds / $values[$i]);
	    $t2 = intval(($elapsedTimeInSeconds - $t1 * $values[$i]) / $values[$i - 1]);
	    return $t1 . $space . $names[$i] . " $t2" . $space . $names[$i - 1];
	    }
	}

    public static function step($echo = false)
	{
	static::$counter++;
	static::$now = microtime(true);
	static::$elapsedTime = static::$now - static::$start;
	static::$averageTime = static::$elapsedTime / static::$counter;
	static::$progress = static::$counter / static::$total;
	static::$percentage = 100.00 * static::$progress;
	static::$estimatedTotalTime = static::$averageTime * static::$total;
	static::$estimatedCompletionIn = static::$averageTime * (static::$total - static::$counter);
	static::$estimatedCompletionTime = date('Y-m-d H:i:s', static::$now + static::$estimatedCompletionIn);
	if ($echo)
	    {
	    echo sprintf(
		    "\r"
		    . "Progress: %.2f%% (%s / %s) "
		    . "Avg. time/item: %.2fs "
		    . "Elapsed time: %s "
		    . "Estimated total time: %s "
		    . "("
		    . "in %s "
		    . "at %s."
		    . ")", static::$percentage, static::$counter, static::$total, static::$averageTime, static::formatTimeInterval(static::$elapsedTime), static::formatTimeInterval(static::$estimatedTotalTime), static::formatTimeInterval(static::$estimatedCompletionIn), static::$estimatedCompletionTime);
	    }
	}

    public static function setTotal($total)
	{
	static::$total = $total;
	}

    public static function getCounter()
	{
	static::$counter;
	}

    public static function getElapsedTime()
	{
	static::$elapsedTime;
	}

    public static function getAverageTime()
	{
	static::$averageTime;
	}

    public static function getEstimatedTotalTime()
	{
	static::$estimatedTotalTime;
	}

    public static function getEstimatedCompletionIn()
	{
	static::$estimatedCompletionIn;
	}

    public static function getPercentage()
	{
	static::$percentage;
	}

    }
