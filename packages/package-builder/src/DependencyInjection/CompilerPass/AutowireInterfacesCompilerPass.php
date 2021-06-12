<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AutowireInterfacesCompilerPass implements CompilerPassInterface
{
    /**
     * @param string[] $typesToAutowire
     */
    public function __construct(
        private array $typesToAutowire
    ) {
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        $containerBuilderDefinitions = $containerBuilder->getDefinitions();
        foreach ($containerBuilderDefinitions as $definition) {
            foreach ($this->typesToAutowire as $typeToAutowire) {
                if (! is_a((string) $definition->getClass(), $typeToAutowire, true)) {
                    continue;
                }

                $definition->setAutowired(true);
                continue 2;
            }
        }
    }
}
