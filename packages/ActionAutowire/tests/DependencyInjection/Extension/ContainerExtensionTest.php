<?php

namespace Symplify\ActionAutowire\Tests\DependencyInjection\Extension;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ActionAutowire\DependencyInjection\Extension\ContainerExtension;
use Symplify\ActionAutowire\DependencyInjection\ServiceLocator;

final class ContainerExtensionTest extends TestCase
{
    public function testLoad()
    {
        $containerExtension = new ContainerExtension();

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->set('container', new Container());

        $containerExtension->load([], $containerBuilder);
        $this->assertCount(2, $containerBuilder->getDefinitions());

        $serviceLocator = $containerBuilder->get('symplify.action_autowire.service_locator');
        $this->assertInstanceOf(ServiceLocator::class, $serviceLocator);
    }
}
