<?php declare(strict_types=1);

namespace Symplify\DependencyInjectionUtils\Adapter\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

trait GetDefinitionByTypeTrait
{
    public function getDefinitionByType(ContainerBuilder $containerBuilder, string $type) : Definition
    {
        foreach ($containerBuilder->getDefinitions() as $definition) {
            if (is_a($definition->getClass(), $type, true)) {
                return $definition;
            }
        }

        throw new \Exception(sprintf(
            'Definition for type %s not found', $type
        ));
    }
}
