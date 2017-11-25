<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

abstract class AbstractSniffTestCase extends TestCase
{
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
        $container = (new ContainerFactory())->create();

        $this->sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->errorAndDiffCollector = $container->get(ErrorAndDiffCollector::class);
    }

    protected function doTest(string $inputFile, string $expectedFile): void
    {
        $result = $this->processFileWithChecker($inputFile);

        $this->assertStringEqualsFile($expectedFile, $result);
    }

    /**
     * File should contain at least 1 error
     */
    protected function doTestWrongFile(string $file): void
    {
        $this->processFileWithChecker($file);

        $this->assertGreaterThanOrEqual(1, $this->errorAndDiffCollector->getErrorCount());
    }

    /**
     * File should contain 0 errors
     */
    protected function doTestCorrectFile(string $file): void
    {
        $this->processFileWithChecker($file);

        $this->assertSame(0, $this->errorAndDiffCollector->getErrorCount());
    }

    abstract protected function createSniff(): Sniff;

    protected function createFileInfo(string $file): SplFileInfo
    {
        return new SplFileInfo($file, '', '');
    }

    protected function processFileWithChecker(string $input): string
    {
        $sniff = $this->createSniff();

        $this->sniffFileProcessor->setSingleSniff($sniff);
        $fileInfo = $this->createFileInfo($input);

        $result = $this->sniffFileProcessor->processFile($fileInfo);

        if ($sniff instanceof DualRunInterface) {
            $result = $this->sniffFileProcessor->processFileSecondRun($fileInfo);
        }

        return $result;
    }
}
