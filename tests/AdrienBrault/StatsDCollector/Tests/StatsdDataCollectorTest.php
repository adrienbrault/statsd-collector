<?php

namespace AdrienBrault\StatsDCollector\Tests;

use AdrienBrault\StatsDCollector\Stat;
use AdrienBrault\StatsDCollector\StatsDDataCollector;
use Hautelook\Frankenstein\TestCase;

class StatsDDataCollectorTest extends TestCase
{
    public function testNoCollectors()
    {
        $collector = new StatsDDataCollector();

        $this
            ->array($collector->collect())
                ->isEmpty()
        ;
    }

    public function test()
    {
        $collectorProphecy = $this->prophesize('AdrienBrault\StatsDCollector\CollectorInterface');
        $collectorProphecy
            ->getStats()
            ->willReturn(array(
                new Stat('metric_foo', 45, array('foo_foo' => 'foo'))
            ))
        ;
        $parameterProviderProphecy = $this->prophesize('AdrienBrault\StatsDCollector\ParameterProviderInterface');
        $parameterProviderProphecy
            ->getParameters()
            ->willReturn(array(
                'http_status' => '404',
                'http_something' => null,
            ))
        ;
        $dataCollector = new StatsDDataCollector(
            array(
                '{app}.{foo_foo}.{http_something}.foobar.{http_status}' => $collectorProphecy->reveal(),
            ),
            array(
                $parameterProviderProphecy->reveal(),
            ),
            array(
                'app' => 'yolo',
            ),
            'null___'
        );

        $this
            ->array($statsData = $dataCollector->collect())
                ->hasSize(1)
            ->object($statData = $statsData[0])
                ->isInstanceOf('Liuggio\StatsdClient\Entity\StatsdData')
                ->and
                ->string($statData->getKey())
                    ->isEqualTo('yolo.foo.null___.foobar.404')
                ->string($statData->getMetric())
                    ->isEqualTo('metric_foo')
                ->variable($statData->getValue())
                    ->isEqualTo(45)
        ;
    }

    public function testInvalidArguments()
    {
        $this
            ->exception(function () {
                new StatsDDataCollector(
                    array(
                        'foo',
                    )
                );
            })
                ->isInstanceOf('InvalidArgumentException')
                ->hasMessage('The template should be a string, integer given ("0").')
            ->exception(function () {
                new StatsDDataCollector(
                    array(
                        'foo' => 'bar',
                    )
                );
            })
                ->isInstanceOf('InvalidArgumentException')
                ->hasMessage('The collector should implement the DataCollectorInterface.')
            ->exception(function () {
                new StatsDDataCollector(
                    array(),
                    array('HELLO')
                );
            })
                ->isInstanceOf('InvalidArgumentException')
                ->hasMessage('The parameter provider should implement the ParameterProviderInterface.')
        ;
    }

    public function testInvalidTemplateParameter()
    {
        $collectorProphecy = $this->prophesize('AdrienBrault\StatsDCollector\CollectorInterface');
        $collectorProphecy
            ->getStats()
            ->willReturn(array(
                new Stat('metric_foo', 45, array('http_status' => '404'))
            ))
        ;
        $dataCollector = new StatsDDataCollector(
            array(
                '{rudrunk?}.foobar.{http_status}' => $collectorProphecy->reveal(),
            ),
            array(),
            array(
                'app' => 'yolo'
            )
        );

        $this
            ->exception(function () use ($dataCollector) {
                $dataCollector->collect();
            })
                ->isInstanceOf('RuntimeException')
                ->hasMessage('Unknown template parameter "rudrunk?", only app, http_status available.')
        ;
    }
}
