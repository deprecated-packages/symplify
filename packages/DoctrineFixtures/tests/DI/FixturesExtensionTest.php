<?php declare(strict_types=1);

namespace Symplify\DoctrineFixtures\Tests\DI;

use Faker\Provider\cs_CZ\Company;
use Nelmio\Alice\Fixtures\Loader;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symplify\DoctrineFixtures\Alice\AliceLoader;
use Symplify\DoctrineFixtures\Contract\Alice\AliceLoaderInterface;
use Symplify\DoctrineFixtures\DI\FixturesExtension;

final class FixturesExtensionTest extends TestCase
{
    public function testLoadConfiguration(): void
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();

        $containerBuilder = $extension->getContainerBuilder();
        $containerBuilder->prepareClassList();

        $aliceLoaderDefinition = $containerBuilder->getDefinitionByType(AliceLoaderInterface::class);

        $this->assertSame(AliceLoader::class, $aliceLoaderDefinition->getClass());
    }

    public function testLoadFakerProvidersToAliceLoader(): void
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

    public function testLoadParsersToAliceLoader(): void
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();
        $extension->beforeCompile();

        $containerBuilder = $extension->getContainerBuilder();
        $aliceLoaderDefinition = $containerBuilder->getDefinitionByType(Loader::class);

        $this->assertSame('addParser', $aliceLoaderDefinition->getSetup()[0]->getEntity());
    }

    private function getExtension(): FixturesExtension
    {
        $extension = new FixturesExtension;
        $extension->setCompiler(new Compiler(new ContainerBuilder), 'fixtures');
        return $extension;
    }
}
