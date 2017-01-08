<?php declare(strict_types=1); 

namespace Symplify\SymfonySecurity\Tests\Core\Authorization;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symplify\SymfonySecurity\Core\Authorization\AccessDecisionManagerFactory;

final class AccessDecisionManagerFactoryTest extends TestCase
{
    public function testCreateWithOneVoter()
    {
        $accessDecisionManagerFactory = new AccessDecisionManagerFactory();
        $voterMock = $this->prophesize(VoterInterface::class);
        $accessDecisionManagerFactory->addVoter($voterMock->reveal());

        $accessDecisionManager = $accessDecisionManagerFactory->create();
        $this->assertInstanceOf(AccessDecisionManager::class, $accessDecisionManager);
    }
}
