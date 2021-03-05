<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;
use Symplify\ConfigTransformer\DependencyInjection\Extension\AliasConfigurableExtension;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

/**
 * This fakes basic extensions, so loading of config is possible without loading real extensions and booting your whole
 * project
 */
final class ExtensionFaker
{
    /**
     * @var YamlKey
     */
    private $yamlKey;

    public function __construct(YamlKey $yamlKey)
    {
        $this->yamlKey = $yamlKey;
    }

    public function fakeInContainerBuilder(ContainerBuilder $containerBuilder, string $yamlContent): void
    {
        $yaml = Yaml::parse($yamlContent, Yaml::PARSE_CUSTOM_TAGS);
        // empty file
        if ($yaml === null) {
            return;
        }

        $rootKeys = array_keys($yaml);

        /** @var string[] $extensionKeys */
        $extensionKeys = array_diff($rootKeys, $this->yamlKey->provideRootKeys());
        if ($extensionKeys === []) {
            return;
        }

        foreach ($extensionKeys as $extensionKey) {
            $aliasConfigurableExtension = new AliasConfigurableExtension($extensionKey);
            $containerBuilder->registerExtension($aliasConfigurableExtension);
        }
    }
}
