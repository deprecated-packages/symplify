<?php declare(strict_types=1);

namespace Symplify\DoctrineMigrations\Tests\DI\MigrationsExtension;

use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symplify\DoctrineMigrations\Configuration\Configuration;
use Symplify\DoctrineMigrations\DI\MigrationsExtension;
use Symplify\SymfonyEventDispatcher\Adapter\Nette\DI\SymfonyEventDispatcherExtension;

class LoadConfigurationTest extends TestCase
{
    /**
     * @var MigrationsExtension
     */
    private $extension;

    protected function setUp(): void
    {
        $containerBuilder = new ContainerBuilder;
        $containerBuilder->parameters = ['appDir' => __DIR__];

        $compiler = new Compiler($containerBuilder);
        $compiler->addExtension('events', new SymfonyEventDispatcherExtension);

        $this->extension = new MigrationsExtension;
        $this->extension->setCompiler($compiler, 'migrations');
    }

    public function testLoadConfiguration(): void
    {
        $this->extension->loadConfiguration();
        $containerBuilder = $this->extension->getContainerBuilder();
        $containerBuilder->prepareClassList();

        $configurationDefinition = $containerBuilder->getDefinition($containerBuilder->getByType(Configuration::class));
        $this->assertSame(Configuration::class, $configurationDefinition->getClass());
    }
}
