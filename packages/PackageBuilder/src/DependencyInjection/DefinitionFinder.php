<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\PackageBuilder\Exception\DependencyInjection\DefinitionForTypeNotFoundException;
use function Safe\sprintf;

final class DefinitionFinder
{
    /**
     * @return Definition[]
     */
    public function findAllByType(ContainerBuilder $containerBuilder, string $type): array
    {
        $definitions = [];
        foreach ($containerBuilder->getDefinitions() as $name => $definition) {
            $class = $definition->getClass() ?: $name;
            if (! is_string($class)) {
                continue;
            }

            if (is_a($class, $type, true)) {
                $definitions[$name] = $definition;
            }
        }

        return $definitions;
    }

    public function getByType(ContainerBuilder $containerBuilder, string $type): Definition
    {
        $definition = self::getByTypeIfExists($containerBuilder, $type);
        if ($definition !== null) {
            return $definition;
        }

        throw new DefinitionForTypeNotFoundException(sprintf('Definition for type "%s" was not found.', $type));
    }

    public function getByTypeIfExists(ContainerBuilder $containerBuilder, string $type): ?Definition
    {
        foreach ($containerBuilder->getDefinitions() as $name => $definition) {
            $class = $definition->getClass() ?: $name;
            if (! is_string($class)) {
                continue;
            }

            if (is_a($class, $type, true)) {
                return $definition;
            }
        }

        return null;
    }
}
