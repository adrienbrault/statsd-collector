<?php

namespace AdrienBrault\StatsDCollector\Tests\Provider;

use AdrienBrault\StatsDCollector\Provider\SymfonyRequestProvider;
use Hautelook\Frankenstein\TestCase;

class SymfonyRequestProviderTest extends TestCase
{
    public function testNoContext()
    {
        $provider = new SymfonyRequestProvider();

        $this
            ->array($provider->getParameters())
                ->isIdenticalTo(array(
                    'request_format' => 'null',
                    'request_scheme' => 'null',
                    'request_ajax' => 'null',
                    'request_locale' => 'null',
                ))
        ;
    }

    public function test()
    {
        $requestProphecy = $this->prophesize('Symfony\Component\HttpFoundation\Request');
        $requestProphecy->getRequestFormat()->willReturn('json');
        $requestProphecy->getScheme()->willReturn('https');
        $requestProphecy->isXmlHttpRequest()->willReturn(false);
        $requestProphecy->getLocale()->willReturn('fr');
        $provider = new SymfonyRequestProvider($requestProphecy->reveal());

        $this
            ->array($provider->getParameters())
                ->isIdenticalTo(array(
                    'request_format' => 'json',
                    'request_scheme' => 'https',
                    'request_ajax' => 'not_ajax',
                    'request_locale' => 'fr',
                ))
        ;
    }
}
