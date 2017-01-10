<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\Definition;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\DefaultAutowire\DependencyInjection\Definition\DefinitionAnalyzer;
use Symplify\DefaultAutowire\DependencyInjection\Definition\DefinitionValidator;
use Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource\EmptyConstructor;
use Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource\MissingArgumentsTypehints;
use Symplify\DefaultAutowire\Tests\DependencyInjection\Definition\DefinitionAnalyzerSource\NotMissingArgumentsTypehints;

final class DefinitionAnalyzerTest extends TestCase
{
    /**
     * @var DefinitionAnalyzer
     */
    private $definitionAnalyzer;

    protected function setUp()
    {
        $this->definitionAnalyzer = new DefinitionAnalyzer(new DefinitionValidator);
    }

    public function testClassHasConstructorArguments()
    {
        $definition = new Definition(EmptyConstructor::class);
        $this->assertFalse($this->definitionAnalyzer->shouldDefinitionBeAutowired(new ContainerBuilder, $definition));
    }

    public function testClassHaveMissingArgumentsTypehints()
    {
        $definition = new Definition(MissingArgumentsTypehints::class);
        $definition->setArguments(['@someService']);

        $this->assertFalse($this->definitionAnalyzer->shouldDefinitionBeAutowired(new ContainerBuilder, $definition));
    }

    public function testClassHaveNotMissingArgumentsTypehints()
    {
        $definition = new Definition(NotMissingArgumentsTypehints::class);
        $definition->setArguments(['@someService']);

        $this->assertTrue($this->definitionAnalyzer->shouldDefinitionBeAutowired(new ContainerBuilder, $definition));
    }
}
