<?php declare(strict_types=1); 

namespace Symplify\SymfonySecurity\Tests\Adapter\Nette\DI\SymfonySecurityExtension;

use Symplify\SymfonySecurity\Contract\Http\FirewallMapFactoryInterface;
use Symplify\SymfonySecurity\Http\FirewallMapFactory;
use Symplify\SymfonySecurity\Tests\Adapter\Nette\DI\AbstractSecurityExtensionTestCase;
use Symplify\SymfonySecurity\Tests\Adapter\Nette\DI\SymfonySecurityExtension\FirewallSource\FirewallHandler;
use Symplify\SymfonySecurity\Tests\Adapter\Nette\DI\SymfonySecurityExtension\FirewallSource\RequestMatcher;

final class FirewallTest extends AbstractSecurityExtensionTestCase
{
    public function testRegisterProperFirewall()
    {
        $extension = $this->getExtension();

        $containerBuilder = $extension->getContainerBuilder();

        $containerBuilder->addDefinition('requestMatcher')
            ->setClass(RequestMatcher::class);

        $containerBuilder->addDefinition('firewallListener')
            ->setClass(FirewallHandler::class);

        $extension->loadConfiguration();

        $containerBuilder->prepareClassList();

        $firewallDefinition = $containerBuilder->getDefinition(
            $containerBuilder->getByType(FirewallMapFactoryInterface::class)
        );
        $this->assertSame(FirewallMapFactory::class, $firewallDefinition->getClass());

        $extension->beforeCompile();
        $this->assertCount(2, $firewallDefinition->getSetup());
    }
}
