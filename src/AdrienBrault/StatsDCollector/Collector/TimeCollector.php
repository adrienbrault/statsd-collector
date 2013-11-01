<?php

namespace AdrienBrault\StatsDCollector\Collector;

use AdrienBrault\StatsDCollector\CollectorInterface;
use AdrienBrault\StatsDCollector\Stat;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class TimeCollector implements CollectorInterface
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getStats()
    {
        if (null === $this->request) {
            return array();
        }

        $startTime = $this->request->server->get('REQUEST_TIME_FLOAT', $this->request->server->get('REQUEST_TIME'));

        if (null === $startTime) {
            return array();
        }

        $elapsedTime = TimeUtil::getElapsedTime($startTime);

        return array(
            new Stat(StatsdDataInterface::STATSD_METRIC_TIMING, $elapsedTime),
        );
    }
}
