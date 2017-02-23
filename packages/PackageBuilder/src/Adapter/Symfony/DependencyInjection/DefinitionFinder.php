<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\ModularDoctrineFilters\Exception\DefinitionForTypeNotFoundException;

final class DefinitionFinder
{
    /**
     * @return Definition[]
     */
    public static function findAllByType(ContainerBuilder $containerBuilder, string $type): array
    {
        $definitions = [];
        foreach ($containerBuilder->getDefinitions() as $name => $definition) {
            if (is_subclass_of($definition->getClass(), $type)) {
                $definitions[$name] = $definition;
            }
        }

        return $definitions;
    }

    public static function getByType(ContainerBuilder $containerBuilder, string $type): Definition
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            if (is_a($definition->getClass(), $type, true)) {
                return $definition;
            }
        }

        throw new DefinitionForTypeNotFoundException(
            sprintf('Definition for type "%s" was not found.', $type)
        );
    }
}
