<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function Safe\class_implements;

/**
 * Inspired by https://github.com/symfony/symfony/pull/25282/files
 * not only for PSR-4, but also covering other manual registration
 */
final class AutowireSinglyImplementedCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $singlyImplemented = $this->collectSinglyImplementedInterfaces($containerBuilder);

        foreach ($singlyImplemented as $interface => $class) {
            $alias = $containerBuilder->setAlias($interface, $class);
            $alias->setPublic(true);
        }
    }

    /**
     * @return string[]
     */
    private function collectSinglyImplementedInterfaces(ContainerBuilder $containerBuilder): array
    {
        $singlyImplemented = [];

        foreach ($containerBuilder->getDefinitions() as $definition) {
            $class = $definition->getClass();
            if (! is_string($class) || ! class_exists($class)) {
                continue;
            }

            foreach (class_implements($class, false) as $interface) {
                $singlyImplemented[$interface] = isset($singlyImplemented[$interface]) ? false : $class;
            }
        }

        return array_filter($singlyImplemented);
    }
}
