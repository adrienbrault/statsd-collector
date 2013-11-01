<?php

namespace AdrienBrault\StatsDCollector\Collector;

class TimeUtil
{
    public static function getCurrentTime()
    {
        return microtime(true);
    }

    public static function getElapsedTime($start)
    {
        return (static::getCurrentTime() - $start) * 1000; // milliseconds
    }
}
