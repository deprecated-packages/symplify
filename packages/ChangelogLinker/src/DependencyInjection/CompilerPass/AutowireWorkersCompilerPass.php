<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

final class AutowireWorkersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            if (! is_a($definition->getClass(), WorkerInterface::class, true)) {
                continue;
            }

            $definition->setAutowired(true);
        }
    }
}
