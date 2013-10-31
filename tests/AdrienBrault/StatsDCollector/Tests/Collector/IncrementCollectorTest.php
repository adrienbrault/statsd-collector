<?php

namespace AdrienBrault\StatsDCollector\Tests\Collector;

use AdrienBrault\StatsDCollector\Collector\IncrementCollector;
use Hautelook\Frankenstein\TestCase;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;

class IncrementCollectorTest extends TestCase
{
    public function test()
    {
        $collector = new IncrementCollector();

        $this
            ->array($stats = $collector->getStats())
                ->hasSize(1)
            ->object($stat = $stats[0])
                ->isInstanceOf('AdrienBrault\StatsDCollector\Stat')
                ->and
                ->variable($stat->getType())
                    ->isEqualTo(StatsdDataInterface::STATSD_METRIC_COUNT)
                ->integer($stat->getValue())
                    ->isEqualTo(1)
                ->array($stat->getParameters())
                    ->isEmpty()
        ;
    }
}
