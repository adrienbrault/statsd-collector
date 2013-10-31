<?php

namespace AdrienBrault\StatsDCollector\Provider;

use AdrienBrault\StatsDCollector\ParameterProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class SymfonyResponseProvider implements ParameterProviderInterface
{
    /**
     * @var Response|null
     */
    private $response;

    public function __construct(Response $response = null)
    {
        $this->response = $response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $this->setResponse($event->getResponse());
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        $code = null;
        $codeType = null;
        $cacheable = null;

        if (null !== $this->response) {
            $code = sprintf('%d', $this->response->getStatusCode());
            $cacheable = $this->response->isCacheable() ? 'cacheable' : 'not_cacheable';

            if ($this->response->isInformational()) {
                $codeType = 'informational';
            } elseif ($this->response->isSuccessful()) {
                $codeType = 'successful';
            } elseif ($this->response->isRedirection()) {
                $codeType = 'redirection';
            } elseif ($this->response->isClientError()) {
                $codeType = 'client_error';
            } elseif ($this->response->isServerError()) {
                $codeType = 'server_error';
            } else {
                $codeType = 'other';
            }
        }

        return array(
            'response_code' => $code,
            'response_code_type' => $codeType,
            'response_cacheable' => $cacheable,
        );
    }
}
