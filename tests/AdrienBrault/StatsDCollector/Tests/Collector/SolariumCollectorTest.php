<?php

namespace AdrienBrault\StatsDCollector\Tests\Collector;

use AdrienBrault\StatsDCollector\Collector\DoctrineDbalCollector;
use AdrienBrault\StatsDCollector\Collector\SolariumCollector;
use Hautelook\Frankenstein\TestCase;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PostExecuteRequest;
use Solarium\Core\Event\PreExecuteRequest;

class SolariumCollectorTest extends TestCase
{
    public function testEmpty()
    {
        $collector = new SolariumCollector();

        $this
            ->array($collector->getStats())
                ->isEmpty()
        ;
    }

    public function testInitPlugin()
    {
        $collector = new SolariumCollector();

        $eventDispatcherProphecy = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcher');
        $eventDispatcherProphecy
            ->addListener(Events::PRE_EXECUTE_REQUEST, array($collector, 'preExecuteRequest'), 1000)
            ->shouldBeCalledTimes(1)
        ;
        $eventDispatcherProphecy
            ->addListener(Events::POST_EXECUTE_REQUEST, array($collector, 'postExecuteRequest'), -1000)
            ->shouldBeCalledTimes(1)
        ;
        $clientProphecy = $this->prophesize('Solarium\Client');
        $clientProphecy
            ->getEventDispatcher()
            ->willReturn($eventDispatcherProphecy->reveal())
        ;

        $collector->initPlugin($clientProphecy->reveal(), array());
    }

    public function test()
    {
        $requestProphecy = $this->prophesize('Solarium\Core\Client\Request');
        $requestProphecy
            ->getMethod()
            ->willReturn('GET')
        ;
        $endpointProphecy = $this->prophesize('Solarium\Core\Client\Endpoint');
        $endpointProphecy
            ->getKey()
            ->willReturn('master')
        ;
        $responseProphecy = $this->prophesize('Solarium\Core\Client\Response');
        $responseProphecy
            ->getStatusCode()
            ->willReturn(200)
        ;
        $collector = new SolariumCollector();
        $collector->preExecuteRequest(
            new PreExecuteRequest($requestProphecy->reveal(), $endpointProphecy->reveal())
        );
        $collector->postExecuteRequest(
            new PostExecuteRequest($requestProphecy->reveal(), $endpointProphecy->reveal(), $responseProphecy->reveal())
        );

        $this
            ->array($stats = $collector->getStats())
                ->hasSize(1)
            ->object($stat = $stats[0])
                ->isInstanceOf('AdrienBrault\StatsDCollector\Stat')
                ->and
                ->variable($stat->getType())
                    ->isEqualTo(StatsdDataInterface::STATSD_METRIC_TIMING)
                ->float($stat->getValue())
                    ->isGreaterThan(0.0)
                ->array($stat->getParameters())
                    ->isEqualTo(array(
                        'solarium_endpoint' => 'master',
                        'solarium_request_method' => 'get',
                        'solarium_response_status' => '200',
                    ))
        ;
    }
}
