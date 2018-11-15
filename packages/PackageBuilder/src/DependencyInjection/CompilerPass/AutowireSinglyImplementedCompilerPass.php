<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use function Safe\class_implements;

/**
 * Inspired by https://github.com/symfony/symfony/pull/25282/files
 * not only for PSR-4, but also covering other manual registration
 */
final class AutowireSinglyImplementedCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        $singlyImplemented = $this->filterSinglyImplementedInterfaces($containerBuilder->getDefinitions());
        foreach ($singlyImplemented as $interface => $class) {
            $alias = $containerBuilder->setAlias($interface, $class);
            $alias->setPublic(true);
        }
    }

    /**
     * @param Definition[] $definitions
     * @return string[]
     */
    private function filterSinglyImplementedInterfaces(array $definitions): array
    {
        $singlyImplemented = [];

        foreach ($definitions as $name => $definition) {
            if ($this->shouldSkipDefinition($definition)) {
                continue;
            }

            $class = $definition->getClass();
            foreach (class_implements($class, false) as $interface) {
                $singlyImplemented[$interface] = isset($singlyImplemented[$interface]) ? false : $name;
            }
        }

        $singlyImplemented = array_filter($singlyImplemented);

        return array_filter($singlyImplemented);
    }

    private function shouldSkipDefinition(Definition $definition): bool
    {
        if ($definition->isAbstract()) {
            return true;
        }

        if ($definition->getClass() === null) {
            return true;
        }

        $class = $definition->getClass();
        if (! is_string($class) || ! class_exists($class)) {
            return true;
        }

        return false;
    }
}
