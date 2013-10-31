<?php

namespace AdrienBrault\StatsDCollector\Tests\Provider;

use AdrienBrault\StatsDCollector\Provider\SymfonyResponseProvider;
use Hautelook\Frankenstein\TestCase;

class SymfonyResponseProviderTest extends TestCase
{
    public function testNoContext()
    {
        $provider = new SymfonyResponseProvider();

        $this
            ->array($provider->getParameters())
                ->isIdenticalTo(array(
                    'response_code' => null,
                    'response_code_type' => null,
                    'response_cacheable' => null,
                ))
        ;
    }

    public function test()
    {
        $responseProphecy = $this->prophesize('Symfony\Component\HttpFoundation\Response');
        $responseProphecy->isCacheable()->willReturn(true);
        $responseProphecy->getStatusCode()->willReturn(202);
        $responseProphecy->isInformational()->willReturn(false);
        $responseProphecy->isSuccessful()->willReturn(true);
        $responseProphecy->isRedirection()->willReturn(false);
        $responseProphecy->isClientError()->willReturn(false);
        $responseProphecy->isServerError()->willReturn(false);
        $provider = new SymfonyResponseProvider($responseProphecy->reveal());

        $this
            ->array($provider->getParameters())
                ->isIdenticalTo(array(
                    'response_code' => '202',
                    'response_code_type' => 'successful',
                    'response_cacheable' => 'cacheable',
                ))
        ;
    }
}
