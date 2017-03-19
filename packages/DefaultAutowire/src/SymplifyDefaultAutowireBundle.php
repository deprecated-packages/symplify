<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\DefaultAutowire\DependencyInjection\Compiler\DefaultAutowireTypesCompilerPass;
use Symplify\DefaultAutowire\DependencyInjection\Compiler\TurnOnAutowireCompilerPass;
use Symplify\DefaultAutowire\DependencyInjection\DefinitionAnalyzer;
use Symplify\DefaultAutowire\DependencyInjection\DefinitionValidator;
use Symplify\DefaultAutowire\DependencyInjection\Extension\SymplifyDefaultAutowireContainerExtension;

final class SymplifyDefaultAutowireBundle extends Bundle
{
    /**
     * @var string
     */
    public const ALIAS = 'symplify_default_autowire';

    public function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new DefaultAutowireTypesCompilerPass);
        $containerBuilder->addCompilerPass(new TurnOnAutowireCompilerPass($this->createDefinitionAnalyzer()));
    }

    public function getContainerExtension(): SymplifyDefaultAutowireContainerExtension
    {
        return new SymplifyDefaultAutowireContainerExtension;
    }

    private function createDefinitionAnalyzer(): DefinitionAnalyzer
    {
        return new DefinitionAnalyzer(new DefinitionValidator);
    }
}
