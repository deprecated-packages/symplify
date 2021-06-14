<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class AliasConfigurableExtension extends Extension
{
    public function __construct(
        private string $alias
    ) {
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param string[] $configs
     */
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
    }
}
