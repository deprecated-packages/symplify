<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\DefaultAutowire\DependencyInjection\Compiler\TurnOnAutowireCompilerPass;
use Symplify\DefaultAutowire\DependencyInjection\Definition\DefinitionAnalyzer;
use Symplify\DefaultAutowire\DependencyInjection\Definition\DefinitionValidator;
use Symplify\DefaultAutowire\Tests\Source\SomeAutowiredService;

final class TurnOnAutowireCompilerPassTest extends TestCase
{
    public function testProcess()
    {
        $containerBuilder = new ContainerBuilder;
        $autowiredDefinition = new Definition(SomeAutowiredService::class);

        $containerBuilder->setDefinition('some_autowired_service', $autowiredDefinition);
        $this->assertFalse($autowiredDefinition->isAutowired());

        $turnOnAutowireCompilerPass = new TurnOnAutowireCompilerPass($this->createDefinitionAnalyzer());
        $turnOnAutowireCompilerPass->process($containerBuilder);
        $this->assertTrue($autowiredDefinition->isAutowired());
    }

    private function createDefinitionAnalyzer() : DefinitionAnalyzer
    {
        return new DefinitionAnalyzer(new DefinitionValidator);
    }
}
