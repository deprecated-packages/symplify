<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Testing\PHPUnit;

use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffResultFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\Testing\Contract\ConfigAwareInterface;
use Symplify\EasyCodingStandard\Testing\Exception\ShouldNotHappenException;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class AbstractCheckerTestCase extends AbstractKernelTestCase implements ConfigAwareInterface
{
    /**
     * @var string[]
     */
    private const POSSIBLE_CODE_SNIFFER_AUTOLOAD_PATHS = [
        __DIR__ . '/../../../../../vendor/squizlabs/php_codesniffer/autoload.php',
        __DIR__ . '/../../../../vendor/squizlabs/php_codesniffer/autoload.php',
    ];

    private FixerFileProcessor $fixerFileProcessor;

    private SniffFileProcessor $sniffFileProcessor;

    private ErrorAndDiffCollector $errorAndDiffCollector;

    private ErrorAndDiffResultFactory $errorAndDiffResultFactory;

    protected function setUp(): void
    {
        // autoload php code sniffer before Kernel boot
        $this->autoloadCodeSniffer();

        $configs = $this->getValidatedConfigs();
        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, $configs);

        $this->fixerFileProcessor = $this->getService(FixerFileProcessor::class);
        $this->sniffFileProcessor = $this->getService(SniffFileProcessor::class);
        $this->errorAndDiffCollector = $this->getService(ErrorAndDiffCollector::class);
        $this->errorAndDiffResultFactory = $this->getService(ErrorAndDiffResultFactory::class);

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

    /**
     * File should stay the same and contain 0 errors
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

            $errorAndDiffResult = $this->errorAndDiffResultFactory->create();

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

        $errorAndDiffResult = $this->errorAndDiffResultFactory->create();
        $this->assertSame($expectedErrorCount, $errorAndDiffResult->getErrorCount(), $message);
    }

    private function doTestWrongToFixedFile(
        SmartFileInfo $wrongFileInfo,
        string $fixedFile,
        SmartFileInfo $fixtureFileInfo
    ): void {
        $processedFileContent = null;
        $this->ensureSomeCheckersAreRegistered();

        if ($this->fixerFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->fixerFileProcessor->processFile($wrongFileInfo);

            $this->assertStringEqualsWithFileLocation($fixedFile, $processedFileContent, $fixtureFileInfo);
        }

        if ($this->sniffFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->sniffFileProcessor->processFile($wrongFileInfo);
        }

        if ($processedFileContent === null) {
            throw new ShouldNotHappenException();
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

    private function ensureSomeCheckersAreRegistered(): void
    {
        $totalCheckersLoaded = count($this->sniffFileProcessor->getCheckers())
            + count($this->fixerFileProcessor->getCheckers());

        if ($totalCheckersLoaded > 0) {
            return;
        }

        throw new ShouldNotHappenException('No checkers were found. Registers them in your config.');
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
}
