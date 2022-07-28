<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;
use Symplify\ConfigTransformer\ValueObject\DependencyInjection\Extension\AliasConfigurableExtension;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

/**
 * This fakes basic extensions, so loading of config is possible without loading real extensions and booting your whole
 * project
 */
final class ExtensionFaker
{
    /**
     * Based mostly on symfony/demo packages @see https://github.com/symfony/demo/tree/main/config/packages
     *
     * @var string[]
     */
    private const COMMON_EXTENSION_NAMES = [
        'assetic',
        'debug',
        'doctrine',
        'doctrine_migrations',
        'framework',
        'hautelook_alice',
        'monolog',
        'nelmio_alice',
        'router',
        'security',
        'twig',
        'web_profiler',
        'zenstruck_foundry'
    ];

    public function fakeInContainerBuilder(ContainerBuilder $containerBuilder, string $yamlContent): void
    {
        $yaml = Yaml::parse($yamlContent, Yaml::PARSE_CUSTOM_TAGS);
        // empty file
        if ($yaml === null) {
            return;
        }

        $rootKeys = array_keys($yaml);

        /** @var string[] $extensionKeys */
        $extensionKeys = array_diff($rootKeys, YamlKey::provideRootKeys());
        if ($extensionKeys === []) {
            return;
        }

        foreach ($extensionKeys as $extensionKey) {
            $aliasConfigurableExtension = new AliasConfigurableExtension($extensionKey);
            $containerBuilder->registerExtension($aliasConfigurableExtension);
        }
    }

    public function fakeGenericExtensionsInContainerBuilder(ContainerBuilder $containerBuilder): void
    {
        foreach (self::COMMON_EXTENSION_NAMES as $extensionName) {
            $aliasConfigurableExtension = new AliasConfigurableExtension($extensionName);
            $containerBuilder->registerExtension($aliasConfigurableExtension);
        }
    }
}
