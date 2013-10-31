<?php

namespace AdrienBrault\StatsDCollector\Provider;

use AdrienBrault\StatsDCollector\ParameterProviderInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class SymfonyUserProvider implements ParameterProviderInterface
{
    /**
     * @var SecurityContextInterface|null
     */
    private $securityContext;

    public function __construct(SecurityContextInterface $securityContext = null)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        $status = 'null';
        $username = 'null';

        if (null !== $this->securityContext) {
            try {
                if ($this->securityContext->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)) {
                    $status = 'full';
                } elseif ($this->securityContext->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_REMEMBERED)) {
                    $status = 'remember_me';
                } elseif ($this->securityContext->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_ANONYMOUSLY)) {
                    $status = 'anonymous';
                }

                $token = $this->securityContext->getToken();
                if (null !== $token) {
                    $username = $token->getUsername();
                }
            } catch (AuthenticationCredentialsNotFoundException $exception) {
                //do nothing
            }
        }

        return array(
            'user_status' => $status,
            'user_username' => $username,
        );
    }
}
