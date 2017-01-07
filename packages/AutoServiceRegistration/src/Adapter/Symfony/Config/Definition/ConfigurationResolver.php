<?php

declare(strict_types=1);

namespace Symplify\AutoServiceRegistration\Adapter\Symfony\Config\Definition;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\AutoServiceRegistration\Adapter\Symfony\SymplifyAutoServiceRegistrationBundle;

final class ConfigurationResolver
{
    /**
     * @var string[]
     */
    private $resolvedConfiguration;
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
    }

    /**
     * @return string[]
     */
    public function getDirectoriesToScan() : array
    {
        return $this->getResolvedConfiguration()[Configuration::DIRECTORIES_TO_SCAN];
    }

    /**
     * @return string[]
     */
    public function getClassSuffixesToSeek() : array
    {
        return $this->getResolvedConfiguration()[Configuration::CLASS_SUFFIXES_TO_SEEK];
    }

    /**
     * @return mixed[]
     */
    private function getResolvedConfiguration() : array
    {
        if ($this->resolvedConfiguration) {
            return $this->resolvedConfiguration;
        }

        $configs = $this->containerBuilder->getExtensionConfig(SymplifyAutoServiceRegistrationBundle::ALIAS);
        $configs = (new Processor())->processConfiguration(new Configuration(), $configs);

        return $this->resolvedConfiguration = $this->containerBuilder->getParameterBag()
            ->resolveValue($configs);
    }
}
