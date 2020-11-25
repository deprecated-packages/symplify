<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig;

use Symplify\PackageBuilder\Neon\NeonPrinter;
use Symplify\PHPStanPHPConfig\CaseConverter\ParameterConverter;
use Symplify\PHPStanPHPConfig\CaseConverter\ServicesConverter;
use Symplify\PHPStanPHPConfig\ContainerBuilderFactory\SymfonyContainerBuilderFactory;
use Symplify\PHPStanPHPConfig\DataCollector\ImportsDataCollector;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\PHPStanPHPConfig\Tests\PHPStanPHPToNeonConverter\PHPStanPHPToNeonConverterTest
 */
final class PHPStanPHPToNeonConverter
{
    /**
     * @var SymfonyContainerBuilderFactory
     */
    private $symfonyContainerBuilderFactory;

    /**
     * @var ImportsDataCollector
     */
    private $importsDataCollector;

    /**
     * @var NeonPrinter
     */
    private $neonPrinter;

    /**
     * @var ParameterConverter
     */
    private $parameterConverter;

    /**
     * @var ServicesConverter
     */
    private $servicesConverter;

    public function __construct(
        SymfonyContainerBuilderFactory $symfonyContainerBuilderFactory,
        ImportsDataCollector $importsDataCollector,
        NeonPrinter $neonPrinter,
        ParameterConverter $parameterConverter,
        ServicesConverter $servicesConverter
    ) {
        $this->symfonyContainerBuilderFactory = $symfonyContainerBuilderFactory;
        $this->importsDataCollector = $importsDataCollector;
        $this->neonPrinter = $neonPrinter;
        $this->parameterConverter = $parameterConverter;
        $this->servicesConverter = $servicesConverter;
    }

    public function convert(SmartFileInfo $phpConfigFileInfo): string
    {
        $containerBuilder = $this->symfonyContainerBuilderFactory->createFromConfig($phpConfigFileInfo);
        $parameterBag = $containerBuilder->getParameterBag();

        $phpStanNeon = [];

        $includes = $this->createIncludes();
        if ($includes !== []) {
            $phpStanNeon['includes'] = $includes;
        }

        $neonParameters = $this->parameterConverter->convertParameterBag($parameterBag);
        if ($neonParameters !== []) {
            $phpStanNeon['parameters'] = $neonParameters;
        }

        $services = $this->servicesConverter->convertContainerBuilder($containerBuilder);
        if ($services !== []) {
            $phpStanNeon['services'] = $services;
        }

        return $this->neonPrinter->printNeon($phpStanNeon);
    }

    /**
     * @return string[]
     */
    private function createIncludes(): array
    {
        return $this->importsDataCollector->getFilePaths();
    }
}
