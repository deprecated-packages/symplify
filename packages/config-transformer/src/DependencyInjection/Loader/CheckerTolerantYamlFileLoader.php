<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\DependencyInjection\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symplify\PhpConfigPrinter\Yaml\CheckerServiceParametersShifter;

/**
 * @see https://github.com/symplify/config-transformer/commit/0244abf3953eb0c5578d203b75749545f705c2a3
 */
final class CheckerTolerantYamlFileLoader extends YamlFileLoader
{
    private CheckerServiceParametersShifter $checkerServiceParametersShifter;

    public function __construct(ContainerBuilder $containerBuilder, FileLocatorInterface $fileLocator)
    {
        $this->checkerServiceParametersShifter = new CheckerServiceParametersShifter();

        parent::__construct($containerBuilder, $fileLocator);
    }

    /**
     * @param string $file
     * @return mixed[]
     */
    protected function loadFile($file): array
    {
        /** @var mixed[]|null $configuration */
        $configuration = parent::loadFile($file);
        if ($configuration === null) {
            return [];
        }

        return $this->checkerServiceParametersShifter->process($configuration);
    }
}
