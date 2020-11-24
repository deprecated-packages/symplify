<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class SymfonyRouteUsageExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../../config'));
        $phpFileLoader->load('config.php');
    }

    public function prepend(ContainerBuilder $containerBuilder): void
    {
        // @see https://symfony.com/doc/current/bundles/prepend_extension.html#more-than-one-bundle-using-prependextensioninterface
        $containerBuilder->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => [
                    'SymfonyRouteUsage' => [
                        'prefix' => 'Symplify\SymfonyRouteUsage\Entity\\',
                        'type' => 'annotation',
                        'is_bundle' => false,
                        'dir' => __DIR__ . '/../../src/Entity',
                    ],
                ],
            ],
        ]);
    }
}
