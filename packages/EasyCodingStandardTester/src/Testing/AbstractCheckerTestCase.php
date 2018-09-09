<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandardTester\Testing;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\Configuration\Exception\NoCheckersLoadedException;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\FileSystem\FileGuard;
use Symplify\PackageBuilder\Finder\SymfonyFileInfoFactory;

abstract class AbstractCheckerTestCase extends TestCase
{
    /**
     * @var ContainerInterface[]
     */
    protected static $cachedContainers = [];

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
     * @var FileGuard
     */
    private $fileGuard;

    protected function setUp(): void
    {
        $this->fileGuard = new FileGuard();
        $this->fileGuard->ensureFileExists($this->provideConfig(), static::class);

        $container = $this->getContainer();

        $this->fixerFileProcessor = $container->get(FixerFileProcessor::class);
        $this->sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->errorAndDiffCollector = $container->get(ErrorAndDiffCollector::class);

        // reset error count from previous possibly container cached run
        $this->errorAndDiffCollector->resetCounters();

        parent::setUp();
    }

    abstract protected function provideConfig(): string;

    /**
     * File should stay the same and contain 0 errors
     * @todo resolve their combination with PSR-12
     */
    protected function doTestCorrectFile(string $correctFile): void
    {
        $this->ensureSomeCheckersAreRegistered();
        $this->fileGuard->ensureFileExists($correctFile, __METHOD__);

        $symfonyFileInfo = SymfonyFileInfoFactory::createFromFilePath($correctFile);

        if ($this->fixerFileProcessor->getCheckers()) {
            $processedFileContent = $this->fixerFileProcessor->processFile($symfonyFileInfo);

            $this->assertStringEqualsFile($correctFile, $processedFileContent);
        }

        if ($this->sniffFileProcessor->getCheckers()) {
            $processedFileContent = $this->sniffFileProcessor->processFile($symfonyFileInfo);

            $this->assertSame(0, $this->errorAndDiffCollector->getErrorCount());
            $this->assertStringEqualsFile($correctFile, $processedFileContent);
        }
    }

    /**
     * @todo resolve their combination with PSR-12
     */
    protected function doTestWrongToFixedFile(string $wrongFile, string $fixedFile): void
    {
        $this->ensureSomeCheckersAreRegistered();
        $this->fileGuard->ensureFileExists($wrongFile, __METHOD__);
        $this->fileGuard->ensureFileExists($fixedFile, __METHOD__);

        $symfonyFileInfo = SymfonyFileInfoFactory::createFromFilePath($wrongFile);

        if ($this->fixerFileProcessor->getCheckers()) {
            $processedFileContent = $this->fixerFileProcessor->processFile($symfonyFileInfo);
            $this->assertStringEqualsFile($fixedFile, $processedFileContent);
        }

        if ($this->sniffFileProcessor->getCheckers()) {
            $processedFileContent = $this->sniffFileProcessor->processFile($symfonyFileInfo);
            if ($this->sniffFileProcessor->getDualRunCheckers()) {
                $processedFileContent = $this->sniffFileProcessor->processFileSecondRun($symfonyFileInfo);
            }
        }

        $this->assertStringEqualsFile($fixedFile, $processedFileContent);
    }

    /**
     * @todo resolve their combination with PSR-12
     */
    protected function doTestWrongFile(string $wrongFile): void
    {
        $this->ensureSomeCheckersAreRegistered();

        $symfonyFileInfo = SymfonyFileInfoFactory::createFromFilePath($wrongFile);

        $this->sniffFileProcessor->processFile($symfonyFileInfo);
        if ($this->sniffFileProcessor->getDualRunCheckers()) {
            $this->sniffFileProcessor->processFileSecondRun($symfonyFileInfo);
        }

        $this->assertGreaterThanOrEqual(1, $this->errorAndDiffCollector->getErrorCount());
    }

    private function ensureSomeCheckersAreRegistered(): void
    {
        $totalCheckersLoaded = count($this->sniffFileProcessor->getCheckers())
            + count($this->fixerFileProcessor->getCheckers());

        if ($totalCheckersLoaded === 0) {
            throw new NoCheckersLoadedException(
                'No checkers were found. Registers them in your config in "services:" '
                . 'section, load them via "--config <file>.yml" or "--level <level> option.'
            );
        }
    }

    private function getContainer(): ContainerInterface
    {
        $fileHash = md5_file($this->provideConfig());
        if (isset(self::$cachedContainers[$fileHash])) {
            return self::$cachedContainers[$fileHash];
        }

        return self::$cachedContainers[$fileHash] = (new ContainerFactory())->createWithConfigs(
            [$this->provideConfig()]
        );
    }
}
