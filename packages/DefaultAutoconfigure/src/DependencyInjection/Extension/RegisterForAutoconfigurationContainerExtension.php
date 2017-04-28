<?php declare(strict_types=1);

namespace Symplify\DefaultAutoconfigure\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symplify\DefaultAutoconfigure\ClassToTagMap;

final class RegisterForAutoconfigurationContainerExtension extends Extension
{
    /**
     * @param mixed[] $configs
     */
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        if ($this->isFrameworkBundleRegistered($containerBuilder)) {
            return;
        }

        foreach (ClassToTagMap::getMap() as $classType => $tag) {
            $childDefinition = $containerBuilder->registerForAutoconfiguration($classType);
            $childDefinition->addTag($tag);
        }
    }

    private function isFrameworkBundleRegistered(ContainerBuilder $containerBuilder): bool
    {
        if (! $containerBuilder->hasParameter('kernel.bundles')) {
            return false;
        }

        if (! isset($containerBuilder->getParameter('kernel.bundles')['FrameworkBundle'])) {
            return false;
        }

        return true;
    }
}
