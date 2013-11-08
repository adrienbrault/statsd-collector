<?php

namespace AdrienBrault\StatsDCollector\Tests\Collector;

use AdrienBrault\StatsDCollector\Collector\DoctrineDbalCollector;
use Hautelook\Frankenstein\TestCase;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;

class DoctrineDbalCollectorTest extends TestCase
{
    public function testEmpty()
    {
        $collector = new DoctrineDbalCollector();

        $this
            ->array($collector->getStats())
                ->isEmpty()
        ;
    }

    public function test()
    {
        $collector = new DoctrineDbalCollector();
        $collector->startQuery('SeLeCt * FROM fOo;');
        $collector->stopQuery();
        $collector->startQuery('UPDATE fOo SET a=b;');
        $collector->stopQuery();
        $collector->startQuery('DELETE FROM fOo;');
        $collector->stopQuery();
        $collector->startQuery('CALL proc();');
        $collector->stopQuery();

        $this
            ->array($stats = $collector->getStats())
                ->hasSize(4)
            ->object($stat = $stats[0])
                ->isInstanceOf('AdrienBrault\StatsDCollector\Stat')
                ->and
                ->variable($stat->getType())
                    ->isEqualTo(StatsdDataInterface::STATSD_METRIC_TIMING)
                ->float($stat->getValue())
                    ->isGreaterThan(0.0)
                ->array($stat->getParameters())
                    ->isEqualTo(array(
                        'query_type' => 'select',
                        'query_table' => 'foo',
                    ))
            ->object($stat = $stats[1])
                ->isInstanceOf('AdrienBrault\StatsDCollector\Stat')
                ->and
                ->variable($stat->getType())
                    ->isEqualTo(StatsdDataInterface::STATSD_METRIC_TIMING)
                ->float($stat->getValue())
                    ->isGreaterThan(0.0)
                ->array($stat->getParameters())
                    ->isEqualTo(array(
                        'query_type' => 'update',
                        'query_table' => 'foo',
                    ))
            ->object($stat = $stats[2])
                ->isInstanceOf('AdrienBrault\StatsDCollector\Stat')
                ->and
                ->variable($stat->getType())
                    ->isEqualTo(StatsdDataInterface::STATSD_METRIC_TIMING)
                ->float($stat->getValue())
                    ->isGreaterThan(0.0)
                ->array($stat->getParameters())
                    ->isEqualTo(array(
                        'query_type' => 'delete',
                        'query_table' => 'foo',
                    ))
            ->object($stat = $stats[3])
                ->isInstanceOf('AdrienBrault\StatsDCollector\Stat')
                ->and
                ->variable($stat->getType())
                    ->isEqualTo(StatsdDataInterface::STATSD_METRIC_TIMING)
                ->float($stat->getValue())
                    ->isGreaterThan(0.0)
                ->array($stat->getParameters())
                    ->isEqualTo(array(
                        'query_type' => 'call',
                        'query_table' => 'proc',
                    ))
        ;
    }
}
