<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\SameNamedParamFamilyRule\Fixture;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ConfigurationExtensionInterface;

final class SkipContainerBuilderMissmatch implements ConfigurationExtensionInterface
{
    public function getConfiguration(array $config, ContainerBuilder $containerBuilder)
    {
    }
}
