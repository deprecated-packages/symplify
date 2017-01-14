<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs;

use Nette\Utils\Finder;
use Nette\Utils\Strings;
use File;
use PHP_CodeSniffer\Runner;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

abstract class AbstractSniffTestCase extends TestCase
{
    /**
     * @var Runner
     */
    private $codeSniffer;

    protected function runSniffTestForDirectory(string $sniffName, string $directory) : void
    {
        $this->codeSniffer = $this->createCodeSnifferWithSniff($sniffName);

        foreach ($this->findFilesInDirectory($directory) as $file) {
            if (Strings::startsWith($file->getFilename(), 'correct')) {
                $this->runSniffTestForCorrectFile($file);
            } elseif (Strings::startsWith($file->getFilename(), 'wrong')) {
                $this->runSniffTestForWrongFile($file);
            }
        }
    }

    private function runSniffTestForCorrectFile(SplFileInfo $file) : void
    {
        $this->codeSniffer->processFile($file->getPath());

        $errorCount = $this->codeSniffer->processFile($file->getPath();
            ->getErrorCount();

        $this->assertSame(
            0,
            $errorCount,
            sprintf(
                'File "%s" should have at 0 errors. %s found.',
                $file->getPathname(),
                $errorCount
            )
        );
    }

    private function runSniffTestForWrongFile(SplFileInfo $file) : void
    {
        $processedFile = $this->codeSniffer->processFile($file->getPathname());
        $errorCount = $processedFile->getErrorCount();

        $this->assertSame(
            1,
            $errorCount,
            sprintf('File "%s" should have at least 1 error.', $file->getPathname())
        );

        $this->runSniffFixerTestIfPresent($file, $processedFile);
    }

    /**
     * @return SplFileInfo[]
     */
    private function findFilesInDirectory(string $directory) : array
    {
        $iterator = Finder::findFiles('*.php.inc')
            ->exclude('*-fixed*')
            ->from($directory)
            ->getIterator();

        return iterator_to_array($iterator);
    }

    private function runSniffFixerTestIfPresent(SplFileInfo $file, File $processedFile) : void
    {
        $fixedFileName = $this->getFixedFileName($file);

        if (! file_exists($fixedFileName)) {
            return;
        }

        $processedFile->fixer->fixFile();
        $fixedContent = $processedFile->fixer->getContents();

        $this->assertSame(
            file_get_contents($fixedFileName),
            $fixedContent,
            sprintf('File "%s" was not fixed properly.', $file->getPathname())
        );
    }

    private function getFixedFileName(SplFileInfo $file) : string
    {
        return dirname($file->getPathname()) . '/' . $file->getBasename('.php.inc') . '-fixed.php.inc';
    }

    private function createCodeSnifferWithSniff(string $sniffName) : PHP_CodeSniffer
    {
        $codeSniffer = new PHP_CodeSniffer;
        $codeSniffer->initStandard(__DIR__ . '/../../src/SymplifyCodingStandard/ruleset.xml', [$sniffName]);

        return $codeSniffer;
    }
}
