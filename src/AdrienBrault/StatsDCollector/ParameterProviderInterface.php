<?php

namespace AdrienBrault\StatsDCollector;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface ParameterProviderInterface
{
    /**
     * @return array<string, string>
     */
    public function getParameters();
}
