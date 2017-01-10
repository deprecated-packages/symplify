<?php

declare(strict_types=1);

namespace Zenify\DoctrineMigrations\Tests\DI\MigrationsExtension;

use Arachne\EventDispatcher\DI\EventDispatcherExtension;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Zenify\DoctrineMigrations\Configuration\Configuration;
use Zenify\DoctrineMigrations\DI\MigrationsExtension;

class LoadConfigurationTest extends TestCase
{

    /**
     * @var MigrationsExtension
     */
    private $extension;


    protected function setUp()
    {
        $containerBuilder = new ContainerBuilder;
        $containerBuilder->parameters = ['appDir' => __DIR__];

        $compiler = new Compiler($containerBuilder);
        $compiler->addExtension('events', new EventDispatcherExtension);

        $this->extension = new MigrationsExtension;
        $this->extension->setCompiler($compiler, 'migrations');
    }


    public function testLoadConfiguration()
    {
        $this->extension->loadConfiguration();
        $containerBuilder = $this->extension->getContainerBuilder();
        $containerBuilder->prepareClassList();

        $configurationDefinition = $containerBuilder->getDefinition($containerBuilder->getByType(Configuration::class));
        $this->assertSame(Configuration::class, $configurationDefinition->getClass());
    }
}
