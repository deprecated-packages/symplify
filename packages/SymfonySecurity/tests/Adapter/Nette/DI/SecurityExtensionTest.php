<?php declare(strict_types=1);

namespace Symplify\SymfonySecurity\Tests\Adapter\Nette\DI;

use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symplify\SymfonySecurity\Core\Authorization\AccessDecisionManagerFactory;
use Symplify\SymfonySecurity\Tests\Adapter\Nette\DI\SymfonySecurityExtensionSource\SomeVoter;

final class SecurityExtensionTest extends AbstractSecurityExtensionTestCase
{
    public function testLoadConfiguration()
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();

        $containerBuilder = $extension->getContainerBuilder();
        $containerBuilder->prepareClassList();

        $accessDecisionManagerDefinition = $containerBuilder->getDefinition(
            $containerBuilder->getByType(AccessDecisionManager::class)
        );
        $this->assertSame(AccessDecisionManager::class, $accessDecisionManagerDefinition->getClass());
    }

    public function testLoadVoters()
    {
        $extension = $this->getExtension();
        $containerBuilder = $extension->getContainerBuilder();

        $extension->loadConfiguration();

        $containerBuilder->addDefinition('someVoter')
            ->setClass(SomeVoter::class);
        $containerBuilder->prepareClassList();

        $accessDecisionManagerFactoryDefinition = $containerBuilder->getDefinition(
            $containerBuilder->getByType(AccessDecisionManagerFactory::class)
        );

        $this->assertCount(0, $accessDecisionManagerFactoryDefinition->getSetup());

        $extension->beforeCompile();

        $this->assertCount(2, $accessDecisionManagerFactoryDefinition->getSetup());
    }
}
