<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\Config\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symplify\PHPStanPHPConfig\DataCollector\ImportsDataCollector;

final class ImportLessPhpFileLoader extends PhpFileLoader
{
    /**
     * @var ImportsDataCollector
     */
    private $importsDataCollector;

    public function __construct(
        ContainerBuilder $containerBuilder,
        FileLocatorInterface $fileLocator,
        ImportsDataCollector $importsDataCollector
    ) {
        parent::__construct($containerBuilder, $fileLocator);

        $this->importsDataCollector = $importsDataCollector;
    }

    /**
     * Only collect imports, do not actually load them
     */
    public function import(
        $resource,
        $type = null,
        $ignoreErrors = false,
        $sourceResource = null,
        $exclude = null
    ): void {
        $this->importsDataCollector->addImport($resource);
    }
}
