<?php

declare(strict_types=1);

namespace Symplify\NetteAdapterForSymfonyBundles\Tests;

use Nette\DI\Compiler;
use Nette\DI\Config\Loader;
use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symplify\NetteAdapterForSymfonyBundles\DI\NetteAdapterForSymfonyBundlesExtension;

final class NetteAdapterForSymfonyBundlesTest extends TestCase
{
    /**
     * @var NetteAdapterForSymfonyBundlesExtension
     */
    private $extension;

    protected function setUp()
    {
        $this->extension = new NetteAdapterForSymfonyBundlesExtension();
        $compiler = new Compiler(new ContainerBuilder());
        $this->extension->setCompiler($compiler, 'symfonyBundles');

        // simulates required Nette\Configurator default parameters
        $compiler->addConfig([
            'parameters' => [
                'appDir' => '',
                'tempDir' => ContainerFactory::createAndReturnTempDir(),
                'debugMode' => true,
                'productionMode' => true,
                'environment' => '',
            ],
        ]);
    }

    public function testLoadBundlesEmpty()
    {
        $bundles = (new Loader())->load(__DIR__ . '/NetteAdapterForSymfonyBundlesSource/bundles.neon');
        $this->extension->setConfig($bundles);
        $this->extension->loadConfiguration();
        $this->extension->beforeCompile();

        $builder = $this->extension->getContainerBuilder();
        $this->assertGreaterThan(17, $builder->getDefinitions());
    }
}
