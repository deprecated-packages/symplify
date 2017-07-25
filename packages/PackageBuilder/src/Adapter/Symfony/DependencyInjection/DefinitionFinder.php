<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\PackageBuilder\Exception\DependencyInjection\DefinitionForTypeNotFoundException;

final class DefinitionFinder
{
    /**
     * @return Definition[]
     */
    public static function findAllByType(ContainerBuilder $containerBuilder, string $type): array
    {
        $definitions = [];
        foreach ($containerBuilder->getDefinitions() as $name => $definition) {
            $class = $definition->getClass() ?: $name;
            if (is_a($class, $type, true)) {
                $definitions[$name] = $definition;
            }
        }

        return $definitions;
    }

    public static function getByType(ContainerBuilder $containerBuilder, string $type): Definition
    {
        $definition = self::getByTypeIfExists($containerBuilder, $type);
        if ($definition !== null) {
            return $definition;
        }

        throw new DefinitionForTypeNotFoundException(
            sprintf('Definition for type "%s" was not found.', $type)
        );
    }

    public static function getByTypeIfExists(ContainerBuilder $containerBuilder, string $type): ?Definition
    {
        foreach ($containerBuilder->getDefinitions() as $name => $definition) {
            $class = $definition->getClass() ?: $name;
            if (is_a($class, $type, true)) {
                return $definition;
            }
        }

        return null;
    }
}
