<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\Config\LoaderFactory;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PHPStanPHPConfig\Config\Loader\ImportLessPhpFileLoader;
use Symplify\PHPStanPHPConfig\DataCollector\ImportsDataCollector;

final class ImportLessPhpFileLoaderFactory
{
    /**
     * @var ImportsDataCollector
     */
    private $importsDataCollector;

    public function __construct(ImportsDataCollector $importsDataCollector)
    {
        $this->importsDataCollector = $importsDataCollector;
    }

    public function create(ContainerBuilder $containerBuilder, FileLocator $fileLocator): ImportLessPhpFileLoader
    {
        return new ImportLessPhpFileLoader($containerBuilder, $fileLocator, $this->importsDataCollector);
    }
}
