<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig;

use Psr\Container\ContainerInterface;
use Symplify\PackageBuilder\Neon\NeonPrinter;
use Symplify\PHPStanPHPConfig\CaseConverter\ParameterConverter;
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

    public function __construct(
        SymfonyContainerBuilderFactory $symfonyContainerBuilderFactory,
        ImportsDataCollector $importsDataCollector,
        NeonPrinter $neonPrinter,
        ParameterConverter $parameterConverter
    ) {
        $this->symfonyContainerBuilderFactory = $symfonyContainerBuilderFactory;
        $this->importsDataCollector = $importsDataCollector;
        $this->neonPrinter = $neonPrinter;
        $this->parameterConverter = $parameterConverter;
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

        $services = [];
        foreach ($containerBuilder->getDefinitions() as $serviceDefinition) {
            if ($serviceDefinition->getClass() === null) {
                continue;
            }

            if (is_a($serviceDefinition->getClass(), ContainerInterface::class, true)) {
                continue;
            }

            $service = [
                'class' => $serviceDefinition->getClass(),
            ];

            // automatically add a tag
            if (is_a($serviceDefinition->getClass(), 'PHPStan\Rules\Rule', true)) {
                $service['tags'] = ['phpstan.rules.rule'];
            }

            $services[] = $service;
        }

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
