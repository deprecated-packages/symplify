<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandardTester\Testing;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Symplify\EasyCodingStandard\Configuration\Exception\NoCheckersLoadedException;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class AbstractCheckerTestCase extends AbstractKernelTestCase
{
    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    protected function setUp(): void
    {
        $configs = $this->getValidatedConfigs();

        // autoload php code sniffer before Kernel boot
        $this->autoloadCodeSniffer();

        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, $configs);

        $this->fixerFileProcessor = self::$container->get(FixerFileProcessor::class);
        $this->sniffFileProcessor = self::$container->get(SniffFileProcessor::class);
        $this->errorAndDiffCollector = self::$container->get(ErrorAndDiffCollector::class);

        // silent output
        $easyCodingStandardStyle = self::$container->get(EasyCodingStandardStyle::class);
        $easyCodingStandardStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        // reset error count from previous possibly container cached run
        $this->errorAndDiffCollector->resetCounters();
    }

    protected function doTestFileInfo(SmartFileInfo $fileInfo): void
    {
        $fixtureSplitter = new StaticFixtureSplitter();

        $inputFileInfoAndExpectedFileInfo = $fixtureSplitter->splitFileInfoToLocalInputAndExpectedFileInfos($fileInfo);

        $this->doTestWrongToFixedFile(
            $inputFileInfoAndExpectedFileInfo->getInputFileInfo(),
            $inputFileInfoAndExpectedFileInfo->getExpectedFilenfoRealPath(),
            $fileInfo
        );
    }

    protected function getCheckerClass(): string
    {
        // to be implemented
        return '';
    }

    protected function provideConfig(): string
    {
        // use local if not overloaded
        if ($this->getCheckerClass() !== '') {
            $hash = $this->createConfigHash();

            $configFileTempPath = sprintf(sys_get_temp_dir() . '/ecs_temp_tests/config_%s.yaml', $hash);

            // cache for 2nd run, similar to original config one
            if (file_exists($configFileTempPath)) {
                return $configFileTempPath;
            }

            $servicesConfiguration = [
                'services' => [
                    '_defaults' => [
                        // for tests
                        'public' => true,
                        'autowire' => true,
                    ],
                    $this->getCheckerClass() => $this->getCheckerConfiguration() ?: null,
                ],
            ];

            $yamlContent = Yaml::dump($servicesConfiguration, Yaml::DUMP_OBJECT_AS_MAP);

            FileSystem::write($configFileTempPath, $yamlContent);

            return $configFileTempPath;
        }

        // to be implemented
        return '';
    }

    /**
     * @return mixed[]
     */
    protected function getCheckerConfiguration(): ?array
    {
        // to be implemented
        return null;
    }

    /**
     * File should stay the same and contain 0 errors
     * @todo resolve their combination with PSR-12
     */
    protected function doTestCorrectFileInfo(SmartFileInfo $fileInfo): void
    {
        $this->errorAndDiffCollector->resetCounters();
        $this->ensureSomeCheckersAreRegistered();

        if ($this->fixerFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->fixerFileProcessor->processFile($fileInfo);
            $this->assertStringEqualsWithFileLocation($fileInfo->getRealPath(), $processedFileContent, $fileInfo);
        }

        if ($this->sniffFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->sniffFileProcessor->processFile($fileInfo);

            $failedAssertMessage = sprintf(
                'There should be no error in "%s" file, but %d errors found.',
                $this->errorAndDiffCollector->getErrorCount(),
                $fileInfo->getRealPath()
            );
            $this->assertSame(0, $this->errorAndDiffCollector->getErrorCount(), $failedAssertMessage);

            $this->assertStringEqualsWithFileLocation($fileInfo->getRealPath(), $processedFileContent, $fileInfo);
        }
    }

    protected function doTestFileInfoWithErrorCountOf(SmartFileInfo $wrongFileInfo, int $errorCount): void
    {
        $this->ensureSomeCheckersAreRegistered();
        $this->errorAndDiffCollector->resetCounters();

        $this->sniffFileProcessor->processFile($wrongFileInfo);

        $message = sprintf(
            'There should be %d error(s) in "%s" file, but none found.',
            $errorCount,
            $wrongFileInfo->getRealPath()
        );

        $this->assertSame($errorCount, $this->errorAndDiffCollector->getErrorCount(), $message);
    }

    private function doTestWrongToFixedFile(
        SmartFileInfo $wrongFileInfo,
        string $fixedFile,
        SmartFileInfo $fixtureFileInfo
    ): void {
        $this->ensureSomeCheckersAreRegistered();

        if ($this->fixerFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->fixerFileProcessor->processFile($wrongFileInfo);

            $this->assertStringEqualsWithFileLocation($fixedFile, $processedFileContent, $fixtureFileInfo);
        }

        if ($this->sniffFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->sniffFileProcessor->processFile($wrongFileInfo);
        }

        $this->assertStringEqualsWithFileLocation($fixedFile, $processedFileContent, $fixtureFileInfo);
    }

    private function autoloadCodeSniffer(): void
    {
        $possibleAutoloadPaths = [
            __DIR__ . '/../../../../../vendor/squizlabs/php_codesniffer/autoload.php',
            __DIR__ . '/../../../../vendor/squizlabs/php_codesniffer/autoload.php',
        ];

        foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
            if (! file_exists($possibleAutoloadPath)) {
                continue;
            }

            require_once $possibleAutoloadPath;
            return;
        }
    }

    private function createConfigHash(): string
    {
        return Strings::substring(
            md5($this->getCheckerClass() . Json::encode($this->getCheckerConfiguration())),
            0,
            10
        );
    }

    private function ensureSomeCheckersAreRegistered(): void
    {
        $totalCheckersLoaded = count($this->sniffFileProcessor->getCheckers())
            + count($this->fixerFileProcessor->getCheckers());

        if ($totalCheckersLoaded > 0) {
            return;
        }

        throw new NoCheckersLoadedException(
            'No checkers were found. Registers them in your config in "services:" '
            . 'section, load them via "--config <file>.yml" or "--level <level> option.'
        );
    }

    private function assertStringEqualsWithFileLocation(
        string $file,
        string $processedFileContent,
        SmartFileInfo $fixtureFileInfo
    ): void {
        $message = $fixtureFileInfo->getRelativeFilePathFromCwd();
        $this->assertStringEqualsFile($file, $processedFileContent, $message);
    }

    /**
     * @return string[]
     */
    private function getValidatedConfigs(): array
    {
        $config = $this->provideConfig();
        (new FileSystemGuard())->ensureFileExists($config, static::class);

        return [$config];
    }
}
