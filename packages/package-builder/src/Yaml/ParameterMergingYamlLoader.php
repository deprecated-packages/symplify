<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Yaml;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\GlobFileLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\PackageBuilder\Yaml\FileLoader\ParameterMergingYamlFileLoader;

/**
 * @see \Symplify\PackageBuilder\Tests\Yaml\ParameterMergingYamlLoader\ParameterMergingYamlLoaderTest
 */
final class ParameterMergingYamlLoader
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var DelegatingLoader
     */
    private $delegatingLoader;

    public function __construct()
    {
        $fileLocator = new FileLocator();
        $this->containerBuilder = new ContainerBuilder();

        $loaderResolver = new LoaderResolver([
            new GlobFileLoader($fileLocator),
            new ParameterMergingYamlFileLoader($this->containerBuilder, $fileLocator),
        ]);

        $this->delegatingLoader = new DelegatingLoader($loaderResolver);
    }

    public function loadParameterBagFromFile(string $yamlFile): ParameterBagInterface
    {
        $this->delegatingLoader->load($yamlFile);

        return $this->containerBuilder->getParameterBag();
    }
}
