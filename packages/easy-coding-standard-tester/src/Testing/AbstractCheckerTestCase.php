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
use Symplify\EasyTesting\Fixture\FixtureSplitter;
use Symplify\EasyTesting\ValueObject\SplitLine;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class AbstractCheckerTestCase extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    public const SPLIT_LINE = "#-----\n#";

    /**
     * To invalidate new versions
     * @var string
     */
    private const CACHE_VERSION_ID = 'v1';

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

        $this->autoloadTestFixture = false;
    }

    protected function doTestFileInfo(SmartFileInfo $fileInfo): void
    {
        $fixtureSplitter = new FixtureSplitter();
        [$beforeFileInfo, $afterFileInfo] = $fixtureSplitter->splitFileInfoToLocalBeforeAfterFileInfos($fileInfo);

        $this->doTestWrongToFixedFile($beforeFileInfo->getRealPath(), $afterFileInfo->getRealPath());
    }

    /**
     * @param string[]|string[][] $files
     */
    protected function doTestFiles(array $files): void
    {
        foreach ($files as $file) {
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

            $this->assertSame(0, $this->errorAndDiffCollector->getErrorCount(), sprintf(
                'There should be no error in "%s" file, but %d errors found.',
                $this->errorAndDiffCollector->getErrorCount(),
                $smartFileInfo->getRealPath()
            ));

            $this->assertStringEqualsWithFileLocation($file, $processedFileContent);
        }
    }

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

        $this->assertGreaterThanOrEqual(
            1,
            $this->errorAndDiffCollector->getErrorCount(),
            sprintf('There should be at least 1 error in "%s" file, but none found.', $smartFileInfo->getRealPath())
        );
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

    private function processFile(string $file): void
    {
        $fileInfo = new SmartFileInfo($file);

        // ----- fixture regardless the file name
        if (Strings::match($fileInfo->getContents(), SplitLine::SPLIT_LINE)) {
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
        $fixtureSplitter = new FixtureSplitter();
        [$originalContent, $expectedContent] = $fixtureSplitter->splitFileInfoToBeforeAfter($smartFileInfo);

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
