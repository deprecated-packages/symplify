<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandardTester\Testing;

use Migrify\PhpConfigPrinter\HttpKernel\PhpConfigPrinterKernel;
use Migrify\PhpConfigPrinter\YamlToPhpConverter;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Configuration\Exception\NoCheckersLoadedException;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffResultFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

abstract class AbstractCheckerTestCase extends AbstractKernelTestCase
{
    /**
     * @var string[]
     */
    private const POSSIBLE_CODE_SNIFFER_AUTOLOAD_PATHS = [
        __DIR__ . '/../../../../../vendor/squizlabs/php_codesniffer/autoload.php',
        __DIR__ . '/../../../../vendor/squizlabs/php_codesniffer/autoload.php',
    ];

    /**
     * @var YamlToPhpConverter|null
     */
    private static $yamlToPhpConverter;

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

    /**
     * @var ErrorAndDiffResultFactory
     */
    private $errorAndDiffResultFactory;

    protected function setUp(): void
    {
        // autoload php code sniffer before Kernel boot
        $this->autoloadCodeSniffer();

        $configs = $this->getValidatedConfigs();
        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, $configs);

        $this->fixerFileProcessor = self::$container->get(FixerFileProcessor::class);
        $this->sniffFileProcessor = self::$container->get(SniffFileProcessor::class);
        $this->errorAndDiffCollector = self::$container->get(ErrorAndDiffCollector::class);
        $this->errorAndDiffResultFactory = self::$container->get(ErrorAndDiffResultFactory::class);

        // silent output
        $easyCodingStandardStyle = self::$container->get(EasyCodingStandardStyle::class);
        $easyCodingStandardStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        // reset error count from previous possibly container cached run
        $this->errorAndDiffCollector->resetCounters();
    }

    protected function doTestFileInfo(SmartFileInfo $fileInfo): void
    {
        $staticFixtureSplitter = new StaticFixtureSplitter();

        $inputFileInfoAndExpectedFileInfo = $staticFixtureSplitter->splitFileInfoToLocalInputAndExpectedFileInfos(
            $fileInfo
        );

        $this->doTestWrongToFixedFile(
            $inputFileInfoAndExpectedFileInfo->getInputFileInfo(),
            $inputFileInfoAndExpectedFileInfo->getExpectedFileInfoRealPath(),
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
            $configFileTempPath = sprintf(sys_get_temp_dir() . '/ecs_temp_tests/config_%s.php', $hash);

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

            $phpConfigContent = $this->getYamlToPhpConverter()
                ->convertYamlArray($servicesConfiguration);

            $smartFileSystem = new SmartFileSystem();
            $smartFileSystem->dumpFile($configFileTempPath, $phpConfigContent);

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

            $errorAndDiffResult = $this->errorAndDiffResultFactory->create($this->errorAndDiffCollector);

            $failedAssertMessage = sprintf(
                'There should be no error in "%s" file, but %d errors found.',
                $errorAndDiffResult->getErrorCount(),
                $fileInfo->getRealPath()
            );
            $this->assertSame(0, $errorAndDiffResult->getErrorCount(), $failedAssertMessage);

            $this->assertStringEqualsWithFileLocation($fileInfo->getRealPath(), $processedFileContent, $fileInfo);
        }
    }

    protected function doTestFileInfoWithErrorCountOf(SmartFileInfo $wrongFileInfo, int $expectedErrorCount): void
    {
        $this->ensureSomeCheckersAreRegistered();
        $this->errorAndDiffCollector->resetCounters();

        $this->sniffFileProcessor->processFile($wrongFileInfo);

        $message = sprintf(
            'There should be %d error(s) in "%s" file, but none found.',
            $expectedErrorCount,
            $wrongFileInfo->getRealPath()
        );

        $errorAndDiffResult = $this->errorAndDiffResultFactory->create($this->errorAndDiffCollector);
        $this->assertSame($expectedErrorCount, $errorAndDiffResult->getErrorCount(), $message);
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
        foreach (self::POSSIBLE_CODE_SNIFFER_AUTOLOAD_PATHS as $possibleCodeSnifferAutoloadPath) {
            if (! file_exists($possibleCodeSnifferAutoloadPath)) {
                continue;
            }

            require_once $possibleCodeSnifferAutoloadPath;
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
        $relativeFilePathFromCwd = $fixtureFileInfo->getRelativeFilePathFromCwd();
        $this->assertStringEqualsFile($file, $processedFileContent, $relativeFilePathFromCwd);
    }

    /**
     * @return string[]
     */
    private function getValidatedConfigs(): array
    {
        $config = $this->provideConfig();
        $fileSystemGuard = new FileSystemGuard();
        $fileSystemGuard->ensureFileExists($config, static::class);

        return [$config];
    }

    private function getYamlToPhpConverter(): YamlToPhpConverter
    {
        if (self::$yamlToPhpConverter !== null) {
            return self::$yamlToPhpConverter;
        }

        $this->bootKernel(PhpConfigPrinterKernel::class);
        self::$yamlToPhpConverter = self::$container->get(YamlToPhpConverter::class);

        return self::$yamlToPhpConverter;
    }
}
