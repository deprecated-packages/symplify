<?php declare(strict_types=1);

namespace Symplify\SymfonySecurityVoters\Tests\Authorization;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symplify\SymfonySecurityVoters\Authorization\AccessDecisionManagerFactory;

final class AccessDecisionManagerFactoryTest extends TestCase
{
    public function testCreateWithOneVoter()
    {
        $accessDecisionManagerFactory = new AccessDecisionManagerFactory;
        $voterMock = $this->prophesize(VoterInterface::class);
        $accessDecisionManagerFactory->addVoter($voterMock->reveal());

        $accessDecisionManager = $accessDecisionManagerFactory->create();
        $this->assertInstanceOf(AccessDecisionManager::class, $accessDecisionManager);
    }
}
