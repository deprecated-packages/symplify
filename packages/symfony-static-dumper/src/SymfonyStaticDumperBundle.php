<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass;
use Symplify\SymfonyStaticDumper\DependencyInjection\Extension\SymfonyStaticDumperExtension;

final class SymfonyStaticDumperBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder): void
    {
        // @see https://symfony.com/doc/current/service_container/compiler_passes.html#working-with-compiler-passes-in-bundles
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
        $containerBuilder->addCompilerPass(new NamelessConsoleCommandCompilerPass());
    }

    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new SymfonyStaticDumperExtension();
    }
}
