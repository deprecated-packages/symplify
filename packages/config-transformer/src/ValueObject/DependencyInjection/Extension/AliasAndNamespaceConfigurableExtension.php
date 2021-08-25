<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\ValueObject\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class AliasAndNamespaceConfigurableExtension extends Extension
{
    public function __construct(
        private string $alias,
        private string $namespace
    ) {
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param string[] $configs
     */
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
    }
}
