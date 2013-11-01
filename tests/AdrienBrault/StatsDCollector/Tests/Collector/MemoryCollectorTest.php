<?php

namespace AdrienBrault\StatsDCollector\Tests\Collector;

use AdrienBrault\StatsDCollector\Collector\MemoryCollector;
use Hautelook\Frankenstein\TestCase;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;

class MemoryCollectorTest extends TestCase
{
    public function test()
    {
        $collector = new MemoryCollector();

        $this
            ->array($stats = $collector->getStats())
                ->hasSize(1)
            ->object($stat = $stats[0])
                ->isInstanceOf('AdrienBrault\StatsDCollector\Stat')
                ->and
                ->variable($stat->getType())
                    ->isEqualTo(StatsdDataInterface::STATSD_METRIC_TIMING)
                ->integer($stat->getValue())
                    ->isGreaterThan(0)
                ->array($stat->getParameters())
                    ->isEmpty()
        ;
    }
}
