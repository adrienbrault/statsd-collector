<?php

namespace AdrienBrault\StatsDCollector\Tests\Provider;

use AdrienBrault\StatsDCollector\Provider\ServerProvider;
use Hautelook\Frankenstein\TestCase;

class ServerProviderTest extends TestCase
{
    public function test()
    {
        $provider = new ServerProvider();

        $this
            ->array($provider->getParameters())
            ->isIdenticalTo(array(
                'server_hostname' => strtolower(preg_replace('/[^a-z0-9_]/', '_', gethostname())),
            ))
        ;
    }
}
