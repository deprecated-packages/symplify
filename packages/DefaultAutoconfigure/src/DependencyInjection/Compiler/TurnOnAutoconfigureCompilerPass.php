<?php declare(strict_types=1);

namespace Symplify\DefaultAutoconfigure\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\DefaultAutoconfigure\ClassToTagMap;

final class TurnOnAutoconfigureCompilerPass implements CompilerPassInterface
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function process(ContainerBuilder $containerBuilder): void
    {
        $this->containerBuilder = $containerBuilder;

        foreach ($containerBuilder->getDefinitions() as $definition) {
            foreach (ClassToTagMap::getMap() as $classType => $tag) {
                $this->setAutoconfiguredIfRelevant($classType, $definition);
            }
        }
    }

    private function setAutoconfiguredIfRelevant(string $classType, Definition $definition): void
    {
        if ($this->shouldBeSkipped($classType, $definition->getClass())) {
            return;
        }

        $definition->setAutoconfigured(true);
    }

    private function shouldBeSkipped(string $interface, ?string $class): bool
    {
        if ($class === null) {
            return true;
        }

        if ($interface !== $class
            && (! $this->containerBuilder->getReflectionClass($interface)
            || ! $this->containerBuilder->getReflectionClass($class))
        ) {
            return true;
        }

        if ($interface !== $class && ! is_subclass_of($class, $interface)) {
            return true;
        }

        return false;
    }
}
