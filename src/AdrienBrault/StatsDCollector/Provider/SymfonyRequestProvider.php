<?php

namespace AdrienBrault\StatsDCollector\Provider;

use AdrienBrault\StatsDCollector\ParameterProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class SymfonyRequestProvider implements ParameterProviderInterface
{
    /**
     * @var Request|null
     */
    private $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->setRequest($event->getRequest());
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        $method = null;
        $format = null;
        $scheme = null;
        $ajax = null;
        $locale = null;
        $route = null;

        if (null !== $this->request) {
            $method = strtolower($this->request->getMethod());
            $format = $this->request->getRequestFormat();
            $scheme = $this->request->getScheme();
            $ajax = $this->request->isXmlHttpRequest() ? 'ajax' : 'not_ajax';
            $locale = $this->request->getLocale();
            $route = $this->request->attributes->get('_route');
        }

        return array(
            'request_method' => $method,
            'request_format' => $format,
            'request_scheme' => $scheme,
            'request_ajax' => $ajax,
            'request_locale' => $locale,
            'request_route' => $route,
        );
    }
}
