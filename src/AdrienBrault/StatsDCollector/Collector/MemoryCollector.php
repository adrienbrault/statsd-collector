<?php

namespace AdrienBrault\StatsDCollector\Collector;

use AdrienBrault\StatsDCollector\CollectorInterface;
use AdrienBrault\StatsDCollector\Stat;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class MemoryCollector implements CollectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getStats()
    {
        return array(
            new Stat(StatsdDataInterface::STATSD_METRIC_TIMING, $this->getMemoryUsage()),
        );
    }

    /*
     * Calculate the peak used by php in MB.
     *
     * @return int
     */
    private function getMemoryUsage()
    {
        $bit = memory_get_peak_usage(true);
        if ($bit > 1024) {
            return intval($bit / 1024);
        }
        return 0;
    }
}
