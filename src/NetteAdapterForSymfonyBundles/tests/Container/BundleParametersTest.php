<?php

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\Container;

use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use Symplify\NetteAdapterForSymfonyBundles\DI\NetteAdapterForSymfonyBundlesExtension;
use Symplify\NetteAdapterForSymfonyBundles\SymfonyContainerAdapter;
use Symplify\NetteAdapterForSymfonyBundles\Tests\ContainerFactory;

final class BundleParametersTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    public function __construct()
    {
        $this->container = (new ContainerFactory())->createWithConfig(
            __DIR__.'/SymfonyContainerAdapterSource/config/init.neon'
        );
    }

    public function testBundleParameters()
    {
        $symfonyContainerAdapter = $this->getSymfonyContainerAdapter();
        $this->assertTrue($symfonyContainerAdapter->hasParameter('doctrine.orm.proxy_namespace'));
    }

    private function getSymfonyContainerAdapter() : SymfonyContainerAdapter
    {
        return $this->container->getService(
            NetteAdapterForSymfonyBundlesExtension::SYMFONY_CONTAINER_SERVICE_NAME
        );
    }
}
