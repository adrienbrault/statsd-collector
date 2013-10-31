<?php

namespace AdrienBrault\StatsDCollector\Collector;

use AdrienBrault\StatsDCollector\Stat;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Event\Events;
use Solarium\Core\Plugin\PluginInterface;
use Solarium\Core\Event\PreExecuteRequest;
use Solarium\Core\Event\PostExecuteRequest;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request as SolariumRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class SolariumCollector extends AbstractCollector implements PluginInterface
{
    // Solarium has __construct in interfaces ... REALLY ?
    public function __construct($options = null) {}

    private $currentStartTime;

    /**
     * @var SolariumRequest
     */
    private $currentRequest;

    /**
     * @var Endpoint
     */
    private $currentEndpoint;

    /**
     * {@inheritdoc}
     */
    public function initPlugin($client, $options)
    {
        $dispatcher = $client->getEventDispatcher();
        $dispatcher->addListener(Events::PRE_EXECUTE_REQUEST, array($this, 'preExecuteRequest'), 1000);
        $dispatcher->addListener(Events::POST_EXECUTE_REQUEST, array($this, 'postExecuteRequest'), -1000);
    }

    public function preExecuteRequest(PreExecuteRequest $event)
    {
        $this->currentRequest = $event->getRequest();
        $this->currentEndpoint = $event->getEndpoint();
        $this->currentStartTime = microtime(true);
    }

    public function postExecuteRequest(PostExecuteRequest $event)
    {
        $timeElapsed = microtime(true) - $this->currentStartTime;

        if (!isset($this->currentRequest)) {
            throw new \RuntimeException('Request not set');
        }
        if ($this->currentRequest !== $event->getRequest()) {
            throw new \RuntimeException('Requests differ');
        }

        $this->addStat(
            new Stat(
                StatsdDataInterface::STATSD_METRIC_TIMING,
                $timeElapsed * 1000,
                array(
                    'solarium_endpoint' => $this->currentEndpoint->getKey(),
                    'solarium_request_method' => strtolower($event->getRequest()->getMethod()),
                    'solarium_response_status' => sprintf('%d', $event->getResponse()->getStatusCode()),
                )
            )
        );

        $this->currentRequest = null;
        $this->currentStartTime = null;
        $this->currentEndpoint = null;
    }

    public function setOptions($options, $overwrite = false) { }
    public function getOption($name) { }
    public function getOptions() { }
}
