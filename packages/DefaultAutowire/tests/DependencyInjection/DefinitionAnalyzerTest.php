<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\DefaultAutowire\DependencyInjection\DefinitionAnalyzer;
use Symplify\DefaultAutowire\DependencyInjection\DefinitionValidator;
use Symplify\DefaultAutowire\DependencyInjection\MethodAnalyzer;
use Symplify\DefaultAutowire\Tests\DependencyInjection\DefinitionAnalyzerSource\BuiltInArgumentsTypehints;
use Symplify\DefaultAutowire\Tests\DependencyInjection\DefinitionAnalyzerSource\EmptyConstructor;
use Symplify\DefaultAutowire\Tests\DependencyInjection\DefinitionAnalyzerSource\MissingArgumentsTypehints;
use Symplify\DefaultAutowire\Tests\DependencyInjection\DefinitionAnalyzerSource\NotMissingArgumentsTypehints;

final class DefinitionAnalyzerTest extends TestCase
{
    /**
     * @var DefinitionAnalyzer
     */
    private $definitionAnalyzer;

    protected function setUp(): void
    {
        $this->definitionAnalyzer = new DefinitionAnalyzer(new DefinitionValidator, new MethodAnalyzer);
    }

    public function testClassHasConstructorArguments(): void
    {
        $definition = new Definition(EmptyConstructor::class);
        $this->assertFalse($this->definitionAnalyzer->shouldDefinitionBeAutowired(new ContainerBuilder, $definition));
    }

    public function testClassHaveMissingArgumentsTypehints(): void
    {
        $definition = new Definition(MissingArgumentsTypehints::class);
        $definition->setArguments(['@someService']);

        $this->assertFalse($this->definitionAnalyzer->shouldDefinitionBeAutowired(new ContainerBuilder, $definition));
    }

    public function testClassHaveNotMissingArgumentsTypehints(): void
    {
        $definition = new Definition(NotMissingArgumentsTypehints::class);
        $definition->setArguments(['@someService']);

        $this->assertTrue($this->definitionAnalyzer->shouldDefinitionBeAutowired(new ContainerBuilder, $definition));
    }

    public function testClassBuiltInArgumentsTypehints(): void
    {
        $definition = new Definition(BuiltInArgumentsTypehints::class);
        $definition->setArguments(['@someService']);

        $this->assertFalse($this->definitionAnalyzer->shouldDefinitionBeAutowired(new ContainerBuilder, $definition));
    }
}
