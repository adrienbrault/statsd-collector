<?php

namespace AdrienBrault\StatsDCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
interface CollectorInterface
{
    /**
     * @return Stat[]
     */
    public function getStats();
}
