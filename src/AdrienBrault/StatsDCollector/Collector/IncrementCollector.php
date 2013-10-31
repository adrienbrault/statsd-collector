<?php

namespace AdrienBrault\StatsDCollector\Collector;

use AdrienBrault\StatsDCollector\CollectorInterface;
use AdrienBrault\StatsDCollector\Stat;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class IncrementCollector implements CollectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getStats()
    {
        return array(
            new Stat(StatsdDataInterface::STATSD_METRIC_COUNT, 1),
        );
    }
}
