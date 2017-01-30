<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs;

use PHP_CodeSniffer\Util\Tokens;
use SplFileInfo;
use Symplify\PHP7_CodeSniffer\Application\Fixer;
use Symplify\PHP7_CodeSniffer\EventDispatcher\CurrentListenerSniffCodeProvider;
use Symplify\PHP7_CodeSniffer\EventDispatcher\Event\CheckFileTokenEvent;
use Symplify\PHP7_CodeSniffer\EventDispatcher\SniffDispatcher;
use Symplify\PHP7_CodeSniffer\File\File;
use Symplify\PHP7_CodeSniffer\Parser\FileToTokensParser;
use Symplify\PHP7_CodeSniffer\Report\ErrorDataCollector;
use Symplify\PHP7_CodeSniffer\Report\ErrorMessageSorter;

final class SniffRunner
{
    public static function getErrorCountForSniffInFile(string $sniffClass, SplFileInfo $fileInfo) : int
    {
        $errorDataCollector = self::createErrorDataCollector();
        $sniffDispatcher = self::createSniffDispatcherWithSniff($sniffClass);
        $file = self::createFileFromFilePath($fileInfo->getPathname(), $errorDataCollector);

        foreach ($file->getTokens() as $stackPointer => $token) {
            $sniffDispatcher->dispatch(
                $token['code'],
                new CheckFileTokenEvent($file, $stackPointer)
            );
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
        self::setupLegacy();

        $sniffDispatcher = new SniffDispatcher(new CurrentListenerSniffCodeProvider());
        $sniffDispatcher->addSniffListeners([new $sniffClass]);

        return $sniffDispatcher;
    }

    private static function createErrorDataCollector() : ErrorDataCollector
    {
        return new ErrorDataCollector(new CurrentListenerSniffCodeProvider(), new ErrorMessageSorter());
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

    private static function setupLegacy() : void
    {
        if (!defined('PHP_CODESNIFFER_VERBOSITY')) {
            define('PHP_CODESNIFFER_VERBOSITY', 0);
        }
        (new Tokens);
    }
}
