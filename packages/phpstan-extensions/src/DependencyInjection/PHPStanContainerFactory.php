<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\DependencyInjection;

use PHPStan\DependencyInjection\Container;
use PHPStan\DependencyInjection\ContainerFactory;

final class PHPStanContainerFactory
{
    /**
     * @param string[] $configs
     */
    public function createContainer(array $configs): Container
    {
        $containerFactory = new ContainerFactory(getcwd());
        // random for tests cache invalidation in case the container changes
        $tempDirectory = sys_get_temp_dir() . '/_symplify_phpstan_tests/id_' . random_int(0, 1000);

        return $containerFactory->create($tempDirectory, $configs, [], []);
    }
}
