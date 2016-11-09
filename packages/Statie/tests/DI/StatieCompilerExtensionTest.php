<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\DI;

use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symplify\Statie\Application\StatieApplication;
use Symplify\Statie\DI\StatieCompilerExtension;
use Symplify\Statie\Source\SourceFileStorage;

final class StatieCompilerExtensionTest extends TestCase
{
    public function testLoadConfiguration()
    {
        $extension = $this->prepareAndReturnExtension();
        $extension->loadConfiguration();

        $containerBuilder = $extension->getContainerBuilder();

        $this->assertNotEmpty($definitionName = $containerBuilder->getByType(StatieApplication::class));
        $applicationDefinition = $containerBuilder->getDefinition($definitionName);
        $this->assertSame(StatieApplication::class, $applicationDefinition->getClass());
    }

    public function testBeforeCompile()
    {
        $extension = $this->prepareAndReturnExtension();
        $extension->loadConfiguration();
        $extension->beforeCompile();

        $definition = $extension->getDefinitionByType(SourceFileStorage::class);
        $this->assertCount(5, $definition->getSetup());
    }

    private function prepareAndReturnExtension() : StatieCompilerExtension
    {
        $extension = new StatieCompilerExtension();
        $extension->setConfig([
            'sourceDir' => __DIR__ . '/StatieCompilerExtensionSource',
        ]);

        $extension->setCompiler(new Compiler(new ContainerBuilder()), null);

        return $extension;
    }
}
