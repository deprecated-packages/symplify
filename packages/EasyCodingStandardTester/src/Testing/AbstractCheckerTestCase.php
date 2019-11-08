<?php declare(strict_types=1);

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
use Symplify\PackageBuilder\FileSystem\FileSystemGuard;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class AbstractCheckerTestCase extends AbstractKernelTestCase
{
    /**
     * To invalidate new versions
     * @var string
     */
    private const CACHE_VERSION_ID = 'v1';

    /**
     * @var string
     */
    private const SPLIT_LINE = '#-----'.PHP_EOL.'#';

    /**
     * @var bool
     */
    protected $autoloadTestFixture = false;

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
     * @var FileSystemGuard
     */
    private $fileSystemGuard;

    /**
     * @var SmartFileInfo|null
     */
    private $activeFileInfo;

    protected function setUp(): void
    {
        $this->fileSystemGuard = new FileSystemGuard();

        $config = $this->provideConfig();
        $this->fileSystemGuard->ensureFileExists($config, static::class);

        $configs = [$config];

        // for symplify package testing
        // 1. vendor installed
        $tokenRunnerConfig = __DIR__ . '/../../../../../packages/TokenRunner/config/config.yaml';
        if (file_exists($tokenRunnerConfig)) {
            $configs[] = $tokenRunnerConfig;
        }

        // 2. monorepo
        $tokenRunnerConfig = __DIR__ . '/../../../../packages/CodingStandard/packages/TokenRunner/config/config.yaml';
        if (file_exists($tokenRunnerConfig)) {
            $configs[] = $tokenRunnerConfig;
        }

        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, $configs);

        $this->fixerFileProcessor = self::$container->get(FixerFileProcessor::class);
        $this->sniffFileProcessor = self::$container->get(SniffFileProcessor::class);
        $this->errorAndDiffCollector = self::$container->get(ErrorAndDiffCollector::class);

        // silent output
        $easyCodingStandardStyle = self::$container->get(EasyCodingStandardStyle::class);
        $easyCodingStandardStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        // reset error count from previous possibly container cached run
        $this->errorAndDiffCollector->resetCounters();

        $this->autoloadTestFixture = false;
    }

    /**
     * @param string[]|string[][] $files
     * @param callable|null $callback Optional callback, e.g. for clear the cache
     */
    protected function doTestFiles(array $files, ?callable $callback = null): void
    {
        foreach ($files as $file) {
            if ($callback !== null) {
                $callback();
            }

            if (is_array($file)) {
                // 2 files, wrong to fixed
                $this->doTestWrongToFixedFile($file[0], $file[1]);
            } else {
                $this->processFile($file);
            }

            $this->activeFileInfo = null;
        }
    }

    protected function getCheckerClass(): string
    {
        // to be implemented
        return '';
    }

    protected function provideConfig(): string
    {
        if ($this->getCheckerClass() !== '') { // use local if not overloaded
            $hash = $this->createConfigHash();

            $configFileTempPath = sprintf(sys_get_temp_dir() . '/ecs_temp_tests/config_%s.yaml', $hash);

            // cache for 2nd run, similar to original config one
            if (file_exists($configFileTempPath)) {
                return $configFileTempPath;
            }

            $servicesConfiguration = [
                'services' => [
                    '_defaults' => [
                        'public' => true, // for tests
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
     * @param string[] $files
     */
    protected function doTestCorrectFiles(array $files): void
    {
        foreach ($files as $file) {
            $this->doTestCorrectFile($file);
        }
    }

    /**
     * @param string[] $files
     */
    protected function doTestWrongFiles(array $files): void
    {
        foreach ($files as $file) {
            $this->doTestWrongFile($file);
        }
    }

    /**
     * @param string[] $files
     */
    protected function doTestWrongToFixedFiles(array $files): void
    {
        foreach ($files as $file) {
            $this->processFile($file);
        }
    }

    /**
     * File should stay the same and contain 0 errors
     * @todo resolve their combination with PSR-12
     */
    protected function doTestCorrectFile(string $file): void
    {
        $this->errorAndDiffCollector->resetCounters();
        $this->ensureSomeCheckersAreRegistered();

        $smartFileInfo = new SmartFileInfo($file);

        if ($this->fixerFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->fixerFileProcessor->processFile($smartFileInfo);
            $this->assertStringEqualsWithFileLocation($file, $processedFileContent);
        }

        if ($this->sniffFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->sniffFileProcessor->processFile($smartFileInfo);

            if ($this->sniffFileProcessor->getDualRunCheckers() !== []) {
                $this->sniffFileProcessor->processFileSecondRun($smartFileInfo);
            }

            $this->assertSame(0, $this->errorAndDiffCollector->getErrorCount(), sprintf(
                'There should be no error in "%s" file, but %d errors found.',
                $this->errorAndDiffCollector->getErrorCount(),
                $smartFileInfo->getRealPath()
            ));
            $this->assertStringEqualsWithFileLocation($file, $processedFileContent);
        }
    }

    /**
     * @todo resolve their combination with PSR-12
     */
    protected function doTestWrongToFixedFile(string $wrongFile, string $fixedFile): void
    {
        $this->ensureSomeCheckersAreRegistered();

        $smartFileInfo = new SmartFileInfo($wrongFile);

        if ($this->fixerFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->fixerFileProcessor->processFile($smartFileInfo);

            $this->assertStringEqualsWithFileLocation($fixedFile, $processedFileContent);
        }

        if ($this->sniffFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->sniffFileProcessor->processFile($smartFileInfo);
            if ($this->sniffFileProcessor->getDualRunCheckers() !== []) {
                $processedFileContent = $this->sniffFileProcessor->processFileSecondRun($smartFileInfo);
            }
        }

        $this->assertStringEqualsWithFileLocation($fixedFile, $processedFileContent);
    }

    /**
     * @todo resolve their combination with PSR-12
     */
    protected function doTestWrongFile(string $wrongFile): void
    {
        $this->ensureSomeCheckersAreRegistered();
        $this->errorAndDiffCollector->resetCounters();

        $smartFileInfo = new SmartFileInfo($wrongFile);

        $this->sniffFileProcessor->processFile($smartFileInfo);
        if ($this->sniffFileProcessor->getDualRunCheckers() !== []) {
            $this->sniffFileProcessor->reset();
            $this->sniffFileProcessor->processFileSecondRun($smartFileInfo);
        }

        $this->assertGreaterThanOrEqual(
            1,
            $this->errorAndDiffCollector->getErrorCount(),
            sprintf('There should be at least 1 error in "%s" file, but none found.', $smartFileInfo->getRealPath())
        );
    }

    private function processFile(string $file): void
    {
        $fileInfo = new SmartFileInfo($file);

        // ----- fixture regardless the file name
        if (Strings::match($fileInfo->getContents(), self::SPLIT_LINE)) {
            $this->activeFileInfo = $fileInfo;
            $this->doTestFiles([$this->splitContentToOriginalFileAndExpectedFile($fileInfo)]);
            return;
        }

        if (Strings::match($file, '#correct#i')) {
            $this->doTestCorrectFile($file);
            return;
        } elseif (Strings::match($file, '#wrong#i')) {
            $this->doTestWrongFile($file);
            return;
        }

        // fall back to split ----- fixture
        $this->activeFileInfo = $fileInfo;
        $this->doTestFiles([$this->splitContentToOriginalFileAndExpectedFile($fileInfo)]);
    }

    private function createConfigHash(): string
    {
        return Strings::substring(
            md5($this->getCheckerClass() . Json::encode($this->getCheckerConfiguration()) . self::CACHE_VERSION_ID),
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

    private function assertStringEqualsWithFileLocation(string $file, string $processedFileContent): void
    {
        $message = 'Caused by ' . ($this->activeFileInfo !== null ? $this->activeFileInfo->getRealPath() : $file);

        $this->assertStringEqualsFile($file, $processedFileContent, $message);
    }

    /**
     * @return string[]
     */
    private function splitContentToOriginalFileAndExpectedFile(SmartFileInfo $smartFileInfo): array
    {
        if (Strings::match($smartFileInfo->getContents(), self::SPLIT_LINE)) {
            // original → expected
            [$originalContent, $expectedContent] = Strings::split($smartFileInfo->getContents(), self::SPLIT_LINE);
        } else {
            // no changes
            $originalContent = $smartFileInfo->getContents();
            $expectedContent = $originalContent;
        }

        $originalFile = $this->createTemporaryPathWithPrefix($smartFileInfo, 'original');
        $expectedFile = $this->createTemporaryPathWithPrefix($smartFileInfo, 'expected');
        FileSystem::write($originalFile, $originalContent);
        FileSystem::write($expectedFile, $expectedContent);

        // file needs to be autoload to enable reflection
        if ($this->autoloadTestFixture) {
            require_once $originalFile;
        }

        return [$originalFile, $expectedFile];
    }

    private function createTemporaryPathWithPrefix(SmartFileInfo $smartFileInfo, string $prefix): string
    {
        $hash = Strings::substring(md5($smartFileInfo->getRealPath()), 0, 5);

        return sprintf(
            sys_get_temp_dir() . '/ecs_temp_tests/%s_%s_%s',
            $prefix,
            $hash,
            $smartFileInfo->getBasename('.inc')
        );
    }
}
