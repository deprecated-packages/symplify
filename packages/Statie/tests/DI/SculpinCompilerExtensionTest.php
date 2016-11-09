<?php

declare(strict_types=1);

namespace Symplify\Statie\Tests\DI;

use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symplify\Statie\Application\SculpinApplication;
use Symplify\Statie\DI\SculpinCompilerExtension;
use Symplify\Statie\Source\SourceFileStorage;

final class SculpinCompilerExtensionTest extends TestCase
{
    public function testLoadConfiguration()
    {
        $extension = $this->prepareAndReturnExtension();
        $extension->loadConfiguration();

        $containerBuilder = $extension->getContainerBuilder();

        $this->assertNotEmpty($definitionName = $containerBuilder->getByType(SculpinApplication::class));
        $applicationDefinition = $containerBuilder->getDefinition($definitionName);
        $this->assertSame(SculpinApplication::class, $applicationDefinition->getClass());
    }

    public function testBeforeCompile()
    {
        $extension = $this->prepareAndReturnExtension();
        $extension->loadConfiguration();
        $extension->beforeCompile();

        $definition = $extension->getDefinitionByType(SourceFileStorage::class);
        $this->assertCount(5, $definition->getSetup());
    }

    private function prepareAndReturnExtension() : SculpinCompilerExtension
    {
        $extension = new SculpinCompilerExtension();
        $extension->setConfig([
            'sourceDir' => __DIR__ . '/SculpinCompilerExtensionSource',
        ]);

        $extension->setCompiler(new Compiler(new ContainerBuilder()), null);

        return $extension;
    }
}
