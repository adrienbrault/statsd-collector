<?php

namespace AdrienBrault\StatsDCollector\Tests\Collector;

use AdrienBrault\StatsDCollector\Collector\DoctrineDbalCollector;
use AdrienBrault\StatsDCollector\Collector\ExceptionCollector;
use Hautelook\Frankenstein\TestCase;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionCollectorTest extends TestCase
{
    public function testEmpty()
    {
        $collector = new ExceptionCollector();

        $this
            ->array($collector->getStats())
                ->isEmpty()
        ;
    }

    public function test()
    {
        $collector = new ExceptionCollector();
        $collector->collectException(new \Exception());
        $collector->collectException(new \RuntimeException());
        $collector->collectException(new NotFoundHttpException());

        $this
            ->array($stats = $collector->getStats())
            ->hasSize(3)
            ->object($stat = $stats[0])
                ->isInstanceOf('AdrienBrault\StatsDCollector\Stat')
                ->and
                ->variable($stat->getType())
                    ->isEqualTo(StatsdDataInterface::STATSD_METRIC_COUNT)
                ->integer($stat->getValue())
                    ->isEqualTo(1)
                ->array($stat->getParameters())
                    ->isEqualTo(array(
                        'exception_class' => 'Exception',
                    ))
            ->object($stat = $stats[1])
                ->isInstanceOf('AdrienBrault\StatsDCollector\Stat')
                ->and
                ->variable($stat->getType())
                    ->isEqualTo(StatsdDataInterface::STATSD_METRIC_COUNT)
                ->integer($stat->getValue())
                    ->isEqualTo(1)
                ->array($stat->getParameters())
                    ->isEqualTo(array(
                        'exception_class' => 'RuntimeException',
                    ))
            ->object($stat = $stats[2])
                ->isInstanceOf('AdrienBrault\StatsDCollector\Stat')
                ->and
                ->variable($stat->getType())
                    ->isEqualTo(StatsdDataInterface::STATSD_METRIC_COUNT)
                ->integer($stat->getValue())
                    ->isEqualTo(1)
                ->array($stat->getParameters())
                    ->isEqualTo(array(
                        'exception_class' => 'Symfony_Component_HttpKernel_Exception_NotFoundHttpException',
                    ))
        ;
    }
}
