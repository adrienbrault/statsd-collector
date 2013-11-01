<?php

namespace AdrienBrault\StatsDCollector\Provider;

use AdrienBrault\StatsDCollector\ParameterProviderInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ServerProvider implements ParameterProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return array(
            'server_hostname' => strtolower(preg_replace('/[^a-z0-9_]/', '_', gethostname())),
        );
    }
}
