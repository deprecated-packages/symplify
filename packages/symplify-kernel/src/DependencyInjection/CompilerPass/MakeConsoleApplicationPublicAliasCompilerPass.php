<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\DependencyInjection\CompilerPass;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class MakeConsoleApplicationPublicAliasCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $consoleApplicationClass = $this->resolveConsoleApplicationClass($containerBuilder);
        if ($consoleApplicationClass === null) {
            return;
        }

        // add console application alias
        if ($consoleApplicationClass === Application::class) {
            return;
        }

        $containerBuilder->setAlias(Application::class, $consoleApplicationClass)
            ->setPublic(true);
    }

    private function resolveConsoleApplicationClass(ContainerBuilder $containerBuilder): ?string
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            if (! is_a((string) $definition->getClass(), Application::class, true)) {
                continue;
            }

            return $definition->getClass();
        }

        return null;
    }
}
