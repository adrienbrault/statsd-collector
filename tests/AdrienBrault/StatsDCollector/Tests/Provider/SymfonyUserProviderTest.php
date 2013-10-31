<?php

namespace AdrienBrault\StatsDCollector\Tests\Provider;

use AdrienBrault\StatsDCollector\Provider\SymfonyUserProvider;
use Hautelook\Frankenstein\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

class SymfonyUserProviderTest extends TestCase
{
    public function testNoContext()
    {
        $provider = new SymfonyUserProvider();

        $this
            ->array($provider->getParameters())
                ->isIdenticalTo(array(
                    'user_status' => null,
                    'user_username' => null,
                ))
        ;
    }

    public function test()
    {
        $tokenProphecy = $this->prophesize('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $tokenProphecy
            ->getUsername()
            ->willReturn('Adrien')
        ;
        $securityContextProphecy = $this->prophesize('Symfony\Component\Security\Core\SecurityContextInterface');
        $securityContextProphecy
            ->getToken()
            ->willReturn($tokenProphecy->reveal())
        ;
        $securityContextProphecy->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)->willReturn(false);
        $securityContextProphecy->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_REMEMBERED)->willReturn(true);
        $securityContextProphecy->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_ANONYMOUSLY)->willReturn(false);
        $provider = new SymfonyUserProvider($securityContextProphecy->reveal());

        $this
            ->array($provider->getParameters())
                ->isIdenticalTo(array(
                    'user_status' => 'remember_me',
                    'user_username' => 'Adrien',
                ))
        ;
    }
}
