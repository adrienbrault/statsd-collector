<?php

namespace AdrienBrault\StatsDCollector\Tests\Collector;

use AdrienBrault\StatsDCollector\Collector\TimeCollector;
use Hautelook\Frankenstein\TestCase;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;
use Symfony\Component\HttpFoundation\Request;

class TimeCollectorTest extends TestCase
{
    public function testNoRequest()
    {
        $collector = new TimeCollector();

        $this
            ->array($stats = $collector->getStats())
                ->isEmpty()
        ;
    }

    public function test()
    {
        $request = Request::create('/');
        $request->server->set('REQUEST_TIME_FLOAT', microtime(true) - 5);
        $collector = new TimeCollector($request);

        $this
            ->array($stats = $collector->getStats())
                ->hasSize(1)
            ->object($stat = $stats[0])
                ->isInstanceOf('AdrienBrault\StatsDCollector\Stat')
                ->and
                ->variable($stat->getType())
                    ->isEqualTo(StatsdDataInterface::STATSD_METRIC_TIMING)
                ->float($stat->getValue())
                    ->isGreaterThan(4900.0)
                    ->isLessThan(5100.0)
                ->array($stat->getParameters())
                    ->isEmpty()
        ;
    }
}
