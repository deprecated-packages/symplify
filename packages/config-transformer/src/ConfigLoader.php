<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer;

use Nette\Utils\Strings;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symplify\ConfigTransformer\DependencyInjection\ExtensionFaker;
use Symplify\ConfigTransformer\DependencyInjection\Loader\CheckerTolerantYamlFileLoader;
use Symplify\ConfigTransformer\DependencyInjection\LoaderFactory\IdAwareXmlFileLoaderFactory;
use Symplify\ConfigTransformer\Enum\Format;
use Symplify\ConfigTransformer\Exception\NotImplementedYetException;
use Symplify\ConfigTransformer\ValueObject\ContainerBuilderAndFileContent;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ConfigLoader
{
    /**
     * @see https://regex101.com/r/4Uanps/2
     * @var string
     */
    private const PHP_CONST_REGEX = '#!php/const[:\s]\s*(.*)(\s*)#';

    /**
     * @see https://regex101.com/r/M0wxf2/1
     * @var string
     */
    private const UNQUOTED_PARAMETER_REGEX = '#(\w+:\s+)(\%(.*?)%)#';

    public function __construct(
        private IdAwareXmlFileLoaderFactory $idAwareXmlFileLoaderFactory,
        private SmartFileSystem $smartFileSystem,
        private ExtensionFaker $extensionFaker
    ) {
    }

    public function createAndLoadContainerBuilderFromFileInfo(
        SmartFileInfo $smartFileInfo,
    ): ContainerBuilderAndFileContent {
        $containerBuilder = new ContainerBuilder();

        $delegatingLoader = $this->createLoaderBySuffix($containerBuilder, $smartFileInfo->getSuffix());
        $fileRealPath = $smartFileInfo->getRealPath();

        // correct old syntax of tags so we can parse it
        $content = $smartFileInfo->getContents();

        // fake quoting of parameter, as it was removed in Symfony 3.1: https://symfony.com/blog/new-in-symfony-3-1-yaml-deprecations
        $content = Strings::replace(
            $content,
            self::UNQUOTED_PARAMETER_REGEX,
            static fn (array $match): string => $match[1] . '"' . $match[2] . '"'
        );

        if (in_array($smartFileInfo->getSuffix(), [Format::YML, Format::YAML], true)) {
            $content = Strings::replace(
                $content,
                self::PHP_CONST_REGEX,
                static fn ($match): string => '"%const(' . str_replace('\\', '\\\\', $match[1]) . ')%"' . $match[2]
            );
            if ($content !== $smartFileInfo->getContents()) {
                $fileRealPath = sys_get_temp_dir() . '/_migrify_config_tranformer_clean_yaml/' . $smartFileInfo->getFilename();
                $this->smartFileSystem->dumpFile($fileRealPath, $content);
            }

            $this->extensionFaker->fakeInContainerBuilder($containerBuilder, $content);
        }

        $delegatingLoader->load($fileRealPath);

        return new ContainerBuilderAndFileContent($containerBuilder, $content);
    }

    private function createLoaderBySuffix(ContainerBuilder $containerBuilder, string $suffix): DelegatingLoader
    {
        if ($suffix === Format::XML) {
            $idAwareXmlFileLoader = $this->idAwareXmlFileLoaderFactory->createFromContainerBuilder($containerBuilder);
            return $this->wrapToDelegatingLoader($idAwareXmlFileLoader, $containerBuilder);
        }

        if (in_array($suffix, [Format::YML, Format::YAML], true)) {
            $yamlFileLoader = new YamlFileLoader($containerBuilder, new FileLocator());
            return $this->wrapToDelegatingLoader($yamlFileLoader, $containerBuilder);
        }

        if ($suffix === Format::PHP) {
            $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator());
            return $this->wrapToDelegatingLoader($phpFileLoader, $containerBuilder);
        }

        throw new NotImplementedYetException($suffix);
    }

    private function wrapToDelegatingLoader(Loader $loader, ContainerBuilder $containerBuilder): DelegatingLoader
    {
        $globFileLoader = new GlobFileLoader($containerBuilder, new FileLocator());
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator());
        $checkerTolerantYamlFileLoader = new CheckerTolerantYamlFileLoader($containerBuilder, new FileLocator());

        $directoryLoader = new DirectoryLoader($containerBuilder, new FileLocator());

        return new DelegatingLoader(new LoaderResolver([
            $directoryLoader,
            $globFileLoader,
            $phpFileLoader,
            $checkerTolerantYamlFileLoader,
            $loader,
        ]));
    }
}
