<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\DependencyInjection\Extension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class SymplifyKernelExtension extends Extension
{
    /**
     * @param string[] $configs
     */
<<<<<<< HEAD
=======

    /**
     * @param string[] $configs
     */
>>>>>>> 629e94c25... [phpstan] add more types
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../../../config'));
        $phpFileLoader->load('common-config.php');
    }
}
