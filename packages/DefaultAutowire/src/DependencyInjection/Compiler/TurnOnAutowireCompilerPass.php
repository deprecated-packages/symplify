<?php

declare(strict_types=1);

namespace Symplify\DefaultAutowire\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\DefaultAutowire\DependencyInjection\Definition\DefinitionAnalyzer;

final class TurnOnAutowireCompilerPass implements CompilerPassInterface
{
    /**
     * @var DefinitionAnalyzer
     */
    private $definitionAnalyzer;

    public function __construct(DefinitionAnalyzer $definitionAnalyzer)
    {
        $this->definitionAnalyzer = $definitionAnalyzer;
    }

    public function process(ContainerBuilder $containerBuilder)
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            if ($this->definitionAnalyzer->shouldDefinitionBeAutowired($containerBuilder, $definition)) {
                $definition->setAutowired(true);
            }
        }
    }
}
