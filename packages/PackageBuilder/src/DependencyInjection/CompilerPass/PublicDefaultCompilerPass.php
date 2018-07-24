<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Make services public to get rid of troubles.
 */
final class PublicDefaultCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }

        foreach ($containerBuilder->getAliases() as $definition) {
            $definition->setPublic(true);
        }
    }
}
