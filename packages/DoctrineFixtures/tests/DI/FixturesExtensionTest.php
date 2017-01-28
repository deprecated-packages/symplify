<?php declare(strict_types=1);

namespace Zenify\DoctrineFixtures\Tests\DI;

use Faker\Provider\cs_CZ\Company;
use Nelmio\Alice\Fixtures\Loader;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Zenify\DoctrineFixtures\Alice\AliceLoader;
use Zenify\DoctrineFixtures\Contract\Alice\AliceLoaderInterface;
use Zenify\DoctrineFixtures\DI\FixturesExtension;
use Zenify\DoctrineFixtures\Tests\ContainerFactory;

final class FixturesExtensionTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp()
    {
        $this->container = (new ContainerFactory)->create();
    }

    public function testLoadConfiguration()
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();

        $containerBuilder = $extension->getContainerBuilder();
        $containerBuilder->prepareClassList();

        $aliceLoaderDefinition = $containerBuilder->getDefinitionByType(AliceLoaderInterface::class);

        $this->assertSame(AliceLoader::class, $aliceLoaderDefinition->getClass());
    }

    public function testLoadFakerProvidersToAliceLoader()
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();

        $containerBuilder = $extension->getContainerBuilder();
        $containerBuilder->addDefinition('company')
            ->setClass(Company::class);

        $containerBuilder->prepareClassList();

        $extension->beforeCompile();

        $loaderDefinition = $containerBuilder->getDefinitionByType(Loader::class);

        $this->assertSame(Loader::class, $loaderDefinition->getClass());
        $arguments = $loaderDefinition->getFactory()->arguments;
        $this->assertCount(3, $arguments);
        $this->assertArrayHasKey('company', $arguments[1]);
    }

    public function testLoadParsersToAliceLoader()
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();
        $extension->beforeCompile();

        $containerBuilder = $extension->getContainerBuilder();
        $aliceLoaderDefinition = $containerBuilder->getDefinitionByType(Loader::class);

        $this->assertSame('addParser', $aliceLoaderDefinition->getSetup()[0]->getEntity());
    }

    private function getExtension() : FixturesExtension
    {
        $extension = new FixturesExtension;
        $extension->setCompiler(new Compiler(new ContainerBuilder), 'fixtures');
        return $extension;
    }
}
