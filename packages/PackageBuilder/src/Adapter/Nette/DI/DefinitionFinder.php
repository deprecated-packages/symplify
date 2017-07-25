<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Adapter\Nette\DI;

use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;
use Symplify\PackageBuilder\Exception\DependencyInjection\DefinitionForTypeNotFoundException;

final class DefinitionFinder
{
    public static function getByType(ContainerBuilder $containerBuilder, string $type): ServiceDefinition
    {
        $containerBuilder->prepareClassList();

        if ($name = $containerBuilder->getByType($type)) {
            return $containerBuilder->getDefinition($name);
        }

        // for non autowired
        foreach ($containerBuilder->findByType($type) as $definition) {
            return $definition;
        }

        throw new DefinitionForTypeNotFoundException(
            sprintf('Definition for type "%s" was not found.', $type)
        );
    }

    public static function getNameByType(ContainerBuilder $containerBuilder, string $type): string
    {
        $containerBuilder->prepareClassList();

        $name = $containerBuilder->getByType($type);
        if ($name !== null) {
            return $name;
        }

        throw new DefinitionForTypeNotFoundException(sprintf(
            'Definition for type "%s" was not found.',
            $type
        ));
    }
}
