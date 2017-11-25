<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\Contract\Application\DualRunInterface;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;

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

    /**
     * @var Fixer
     */
    private $fixer;

    protected function runSniffTestForDirectory(string $sniffClass, string $directory): void
    {
        $container = (new ContainerFactory())->create();

        $this->sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->errorAndDiffCollector = $container->get(ErrorAndDiffCollector::class);
        $this->fixer = $container->get(Fixer::class);

        foreach ($this->findFilesInDirectory($directory) as $file) {
            if (Strings::startsWith($file->getFilename(), 'correct')) {
                $this->runSniffTestForCorrectFile($sniffClass, $file);
            }

            if (Strings::startsWith($file->getFilename(), 'wrong')) {
                $this->runSniffTestForWrongFile($sniffClass, $file);
            }
        }
    }

    private function runSniffTestForCorrectFile(string $sniffClass, SplFileInfo $fileInfo): void
    {
        $sniff = new $sniffClass();
        $this->processFileWithSniff($sniff, $fileInfo);

        $this->assertSame(0, $this->errorAndDiffCollector->getErrorCount(), sprintf(
            'File "%s" should have no errors. %s found.',
            $fileInfo->getPathname(),
            $this->errorAndDiffCollector->getErrorCount()
        ));
    }

    private function runSniffTestForWrongFile(string $sniffClass, SplFileInfo $fileInfo): void
    {
        /** @var Sniff $sniff */
        $sniff = new $sniffClass();
        $this->processFileWithSniff($sniff, $fileInfo);

        if ($sniff instanceof DualRunInterface) {
            $sniff->increaseRun();
            $this->processFileWithSniff($sniff, $fileInfo);
        }

        $this->assertGreaterThanOrEqual(1, $this->errorAndDiffCollector->getErrorCount(), sprintf(
            'File "%s" should have at least 1 error.',
            $fileInfo->getPathname()
        ));

        $fixedFileName = $this->getFixedFileName($fileInfo);
        if (! is_file($fixedFileName)) {
            return;
        }

        $this->assertStringEqualsFile($fixedFileName, $this->fixer->getContents(), sprintf(
            'File "%s" was not fixed properly. "%s" expected, "%s" given.',
            $fileInfo->getPathname(),
            file_get_contents($fixedFileName),
            $this->fixer->getContents()
        ));
    }

    /**
     * @return SplFileInfo[]
     */
    private function findFilesInDirectory(string $directory): array
    {
        $iterator = Finder::create()
            ->name('*.php.inc')
            ->exclude('*-fixed*')
            ->in($directory)
            ->getIterator();

        return iterator_to_array($iterator);
    }

    private function getFixedFileName(SplFileInfo $fileInfo): string
    {
        return dirname($fileInfo->getPathname()) . '/' . $fileInfo->getBasename('.php.inc') . '-fixed.php.inc';
    }

    private function processFileWithSniff(Sniff $sniff, SplFileInfo $fileInfo): void
    {
        $this->errorAndDiffCollector->resetCounters();
        $this->sniffFileProcessor->setSingleSniff($sniff);
        $this->sniffFileProcessor->processFile($fileInfo);
    }
}
