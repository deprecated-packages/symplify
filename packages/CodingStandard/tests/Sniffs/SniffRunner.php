<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs;

use PHP_CodeSniffer\Sniffs\Sniff;
use SplFileInfo;
use Symplify\SniffRunner\Application\Fixer;
use Symplify\SniffRunner\EventDispatcher\Event\CheckFileTokenEvent;
use Symplify\SniffRunner\EventDispatcher\SniffDispatcher;
use Symplify\SniffRunner\File\File;
use Symplify\SniffRunner\Legacy\LegacyCompatibilityLayer;
use Symplify\SniffRunner\Parser\FileToTokensParser;
use Symplify\SniffRunner\Report\ErrorDataCollector;
use Symplify\SniffRunner\Report\ErrorMessageSorter;

final class SniffRunner
{
    public static function getErrorCountForSniffInFile(string $sniffClass, SplFileInfo $fileInfo) : int
    {
        $errorDataCollector = self::createErrorDataCollector();
        $sniffDispatcher = self::createSniffDispatcherWithSniff($sniffClass);
        $file = self::createFileFromFilePath($fileInfo->getPathname(), $errorDataCollector);

        foreach ($file->getTokens() as $stackPointer => $token) {
            $sniffDispatcher->dispatch($token['code'], new CheckFileTokenEvent($file, $stackPointer));
        }

        return $errorDataCollector->getErrorCount();
    }

    public static function getFixedContentForSniffInFile(string $sniffClass, SplFileInfo $fileInfo) : string
    {
        $sniffDispatcher = self::createSniffDispatcherWithSniff($sniffClass);
        $file = self::createFileFromFilePath($fileInfo->getPathname());

        foreach ($file->getTokens() as $stackPointer => $token) {
            $sniffDispatcher->dispatch(
                $token['code'],
                new CheckFileTokenEvent($file, $stackPointer)
            );
        }

        return $file->fixer->getContents();
    }

    private static function createSniffDispatcherWithSniff(string $sniffClass) : SniffDispatcher
    {
        LegacyCompatibilityLayer::add();

        $sniffDispatcher = new SniffDispatcher();
        $sniffDispatcher->addSniffListeners([new $sniffClass]);

        return $sniffDispatcher;
    }

    private static function createErrorDataCollector() : ErrorDataCollector
    {
        return new ErrorDataCollector(new ErrorMessageSorter());
    }

    private static function createFileFromFilePath(
        string $filePath,
        ErrorDataCollector $errorDataCollector = null
    ) : File {
        $fileToTokenParser = new FileToTokensParser();

        $errorDataCollector = $errorDataCollector ?: self::createErrorDataCollector();

        $tokens = $fileToTokenParser->parseFromFilePath($filePath);

        $fixer = new Fixer();
        $file = new File($filePath, $tokens, $fixer, $errorDataCollector, true);
        $file->fixer->startFile($file);

        return $file;
    }
}
