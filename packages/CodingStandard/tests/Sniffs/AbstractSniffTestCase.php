<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs;

use Nette\Utils\Finder;
use Nette\Utils\Strings;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\EasyCodingStandard\SniffRunner\EventDispatcher\SniffDispatcher;
use Symplify\EasyCodingStandard\SniffRunner\Report\ErrorDataCollector;
use Symplify\EasyCodingStandard\SniffRunner\Report\ErrorMessageSorter;

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
                $this->runSniffTestForCorrectFile($sniffClass, $file);
            }

            if (Strings::startsWith($file->getFilename(), 'wrong')) {
                $this->runSniffTestForWrongFile($sniffClass, $file);
            }
        }
    }

    private function runSniffTestForCorrectFile(string $sniffClass, SplFileInfo $fileInfo) : void
    {
        $errorCount = SniffRunner::getErrorCountForSniffInFile($sniffClass, $fileInfo);
        $this->assertSame(0, $errorCount, sprintf(
            'File "%s" should have at 0 errors. %s found.',
            $fileInfo->getPathname(),
            $errorCount
        ));
    }

    private function runSniffTestForWrongFile(string $sniffClass, SplFileInfo $fileInfo) : void
    {
        $errorCount = SniffRunner::getErrorCountForSniffInFile($sniffClass, $fileInfo);
        $this->assertSame(1, $errorCount, sprintf(
            'File "%s" should have at least 1 error.',
            $fileInfo->getPathname()
        ));

        $fixedFileName = $this->getFixedFileName($fileInfo);
        if (! file_exists($fixedFileName)) {
            return;
        }

        $fixedContent = SniffRunner::getFixedContentForSniffInFile($sniffClass, $fileInfo);
        $this->assertSame(file_get_contents($fixedFileName), $fixedContent, sprintf(
            'File "%s" was not fixed properly. "%s" expected, "%s" given.',
            $fileInfo->getPathname(),
            file_get_contents($fixedFileName),
            $fixedContent
        ));
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

    private function getFixedFileName(SplFileInfo $fileInfo) : string
    {
        return dirname($fileInfo->getPathname()) . '/' . $fileInfo->getBasename('.php.inc') . '-fixed.php.inc';
    }

    private function createSniffDispatcherWithSniff(string $sniffClass) : SniffDispatcher
    {
        $sniffDispatcher = new SniffDispatcher();
        $sniffDispatcher->addSniffListeners([new $sniffClass]);

        return $sniffDispatcher;
    }

    private function createErrorDataCollector() : ErrorDataCollector
    {
        return new ErrorDataCollector(new ErrorMessageSorter());
    }
}
