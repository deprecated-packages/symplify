<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig;

use Nette\Neon\Encoder;
use Nette\Neon\Neon;
use Nette\Utils\Strings;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use Symplify\PHPStanPHPConfig\ContainerBuilderFactory\SymfonyContainerBuilderFactory;
use Symplify\PHPStanPHPConfig\ValueObject\Option;
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

    public function __construct(SymfonyContainerBuilderFactory $symfonyContainerBuilderFactory)
    {
        $this->symfonyContainerBuilderFactory = $symfonyContainerBuilderFactory;
    }

    public function convert(SmartFileInfo $phpConfigFileInfo): string
    {
        $containerBuilder = $this->symfonyContainerBuilderFactory->createFromConfig($phpConfigFileInfo);
        $parameterBag = $containerBuilder->getParameterBag();

        $phpStanNeon = [];
        $neonParameters = $this->createParameters($parameterBag);
        if ($neonParameters !== []) {
            $phpStanNeon['parameters'] = $neonParameters;
        }

        return $this->printNeon($phpStanNeon);
    }

    private function createParameters(ParameterBagInterface $parameterBag): array
    {
        $neonParameters = [];

        if ($parameterBag->has(Option::LEVEL)) {
            $neonParameters[Option::LEVEL] = $parameterBag->get(Option::LEVEL);
        }

        if ($parameterBag->has(Option::PATHS)) {
            $neonParameters[Option::PATHS] = $this->resolvePaths($parameterBag);
        }

        if ($parameterBag->has(Option::REPORT_UNMATCHED_IGNORED_ERRORS)) {
            // to prevent full thread lagging pc
            $neonParameters[Option::REPORT_UNMATCHED_IGNORED_ERRORS] = (bool) $parameterBag->get(
                Option::REPORT_UNMATCHED_IGNORED_ERRORS
            );
        }

        if ($parameterBag->has(Option::PARALLEL_MAX_PROCESSES)) {
            // to prevent full thread lagging pc
            $neonParameters['parallel'][Option::PARALLEL_MAX_PROCESSES] = (int) $parameterBag->get(
                Option::PARALLEL_MAX_PROCESSES
            );
        }

        return $neonParameters;
    }

    /**
     * @param mixed[] $phpStanNeon
     */
    private function printNeon(array $phpStanNeon): string
    {
        $neonContent = Neon::encode($phpStanNeon, Encoder::BLOCK);

        // tabs to spaces for consistency
        $neonContent = Strings::replace($neonContent, '#\t#', '    ');

        return rtrim($neonContent) . PHP_EOL;
    }

    /**
     * @return string[]
     */
    private function resolvePaths(ParameterBagInterface $parameterBag): array
    {
        // relativize paths to root
        $paths = (array) $parameterBag->get(Option::PATHS);

        $relativePaths = [];
        foreach ($paths as $path) {
            $pathFileInfo = new SmartFileInfo($path);
            $relativeFilePath = $pathFileInfo->getRelativeFilePathFromCwd();

            // clear temp file path for tests
            if (StaticPHPUnitEnvironment::isPHPUnitRun()) {
                $relativeFilePath = (string) Strings::after($relativeFilePath, 'easy_testing/');
            }

            $relativePaths[] = $relativeFilePath;
        }

        return $relativePaths;
    }
}
