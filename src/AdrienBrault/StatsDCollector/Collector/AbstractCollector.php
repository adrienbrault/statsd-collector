<?php

namespace AdrienBrault\StatsDCollector\Collector;

use AdrienBrault\StatsDCollector\CollectorInterface;
use AdrienBrault\StatsDCollector\Stat;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class AbstractCollector implements CollectorInterface
{
    /**
     * @var Stat[]
     */
    private $stats = array();

    /**
     * {@inheritdoc}
     */
    public function getStats()
    {
        return $this->stats;
    }

    protected function addStat(Stat $stat)
    {
        $this->stats[] = $stat;
    }
}
