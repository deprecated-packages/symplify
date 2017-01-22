<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs;

use Nette\Utils\Finder;
use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\PHP7_CodeSniffer\Application\Fixer;
use Symplify\PHP7_CodeSniffer\EventDispatcher\CurrentListenerSniffCodeProvider;
use Symplify\PHP7_CodeSniffer\EventDispatcher\Event\CheckFileTokenEvent;
use Symplify\PHP7_CodeSniffer\EventDispatcher\SniffDispatcher;
use Symplify\PHP7_CodeSniffer\File\File;
use Symplify\PHP7_CodeSniffer\Parser\EolCharDetector;
use Symplify\PHP7_CodeSniffer\Parser\FileToTokensParser;
use Symplify\PHP7_CodeSniffer\Report\ErrorDataCollector;
use Symplify\PHP7_CodeSniffer\Report\ErrorMessageSorter;

abstract class AbstractSniffTestCase extends TestCase
{
    /**
     * @var SniffDispatcher
     */
    private $sniffDispatcher;

    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    protected function runSniffTestForDirectory(string $sniffClass, string $directory) : void
    {
        $this->sniffDispatcher = $this->createSniffDispatcherWithSniff($sniffClass);

        foreach ($this->findFilesInDirectory($directory) as $file) {
            $this->errorDataCollector = $this->createErrorDataCollector();
            if (Strings::startsWith($file->getFilename(), 'correct')) {
                $this->runSniffTestForCorrectFile($file);
            }

            if (Strings::startsWith($file->getFilename(), 'wrong')) {
                $this->runSniffTestForWrongFile($file);
            }
        }
    }

    private function runSniffTestForCorrectFile(SplFileInfo $fileInfo) : void
    {
        $file = $this->createFileFromFilePath($fileInfo->getPathname());
        $this->processFile($file);

        $errorCount = $this->errorDataCollector->getErrorCount();

        $this->assertSame(0, $errorCount, sprintf(
            'File "%s" should have at 0 errors. %s found.',
            $fileInfo->getPathname(),
            $errorCount
        ));
    }

    private function runSniffTestForWrongFile(SplFileInfo $fileInfo) : void
    {
        $file = $this->createFileFromFilePath($fileInfo->getPathname());
        $this->processFile($file);

        $errorCount = $this->errorDataCollector->getErrorCount();

        $this->assertSame(
            1,
            $errorCount,
            sprintf('File "%s" should have at least 1 error.', $fileInfo->getPathname())
        );

        $this->runSniffFixerTestIfPresent($fileInfo, $file);
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

    private function createSniffDispatcherWithSniff(string $sniffName) : SniffDispatcher
    {
        // legacy required
        if (!defined('PHP_CODESNIFFER_VERBOSITY')) {
            define('PHP_CODESNIFFER_VERBOSITY', 0);
        }
        // legacy required
        (new Tokens);

        if (class_exists($sniffName)) {
            /** @var Sniff $sniff */
            $sniff = new $sniffName;
        }

        $sniffDispatcher = new SniffDispatcher(new CurrentListenerSniffCodeProvider());
        $sniffDispatcher->addSniffListeners([$sniffName => $sniff]);

        return $sniffDispatcher;
    }

    private function createErrorDataCollector() : ErrorDataCollector
    {
        return new ErrorDataCollector(new CurrentListenerSniffCodeProvider(), new ErrorMessageSorter());
    }

    private function createFileFromFilePath(string $filePath) : File
    {
        $eolCharDetector = new EolCharDetector();
        $fileToTokenParser = new FileToTokensParser($eolCharDetector);

        $tokens = $fileToTokenParser->parseFromFilePath($filePath);
        $eolChar = $eolCharDetector->detectForFilePath($filePath);

        $fixer = new Fixer();
        $file = new File($filePath, $tokens, $fixer, $this->errorDataCollector, true, $eolChar);
        $file->fixer->startFile($file);

        return $file;
    }

    private function processFile(File $file) : void
    {
        foreach ($file->getTokens() as $stackPointer => $token) {
            $this->sniffDispatcher->dispatch(
                $token['code'],
                new CheckFileTokenEvent($file, $stackPointer)
            );
        }
    }
}
