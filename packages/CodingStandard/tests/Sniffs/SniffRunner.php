<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs;

use SplFileInfo;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Contract\ChangedFilesDetectorInterface;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\Error\ErrorSorter;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;
use Symplify\EasyCodingStandard\SniffRunner\Legacy\LegacyCompatibilityLayer;
use Symplify\EasyCodingStandard\SniffRunner\Parser\FileToTokensParser;
use Symplify\EasyCodingStandard\SniffRunner\TokenDispatcher\Event\FileTokenEvent;
use Symplify\EasyCodingStandard\SniffRunner\TokenDispatcher\TokenDispatcher;

final class SniffRunner
{
    public static function getErrorCountForSniffInFile(string $sniffClass, SplFileInfo $fileInfo): int
    {
        $errorDataCollector = self::createErrorDataCollector();
        $sniffDispatcher = self::createSniffDispatcherWithSniff($sniffClass);
        $file = self::createFileFromFilePath($fileInfo->getPathname(), $errorDataCollector);

        foreach ($file->getTokens() as $stackPointer => $token) {
            $sniffDispatcher->dispatchToken(
                $token['code'],
                new FileTokenEvent($file, $stackPointer)
            );
        }

        return $errorDataCollector->getErrorCount();
    }

    public static function getFixedContentForSniffInFile(string $sniffClass, SplFileInfo $fileInfo): string
    {
        $sniffDispatcher = self::createSniffDispatcherWithSniff($sniffClass);
        $file = self::createFileFromFilePath($fileInfo->getPathname());

        foreach ($file->getTokens() as $stackPointer => $token) {
            $sniffDispatcher->dispatchToken(
                $token['code'],
                new FileTokenEvent($file, $stackPointer)
            );
        }

        return $file->fixer->getContents();
    }

    private static function createSniffDispatcherWithSniff(string $sniffClass): TokenDispatcher
    {
        LegacyCompatibilityLayer::add();

        $sniffDispatcher = new TokenDispatcher(
            new Skipper(new ConfigurationNormalizer)
        );
        $sniffDispatcher->addSniffListeners([new $sniffClass]);

        return $sniffDispatcher;
    }

    private static function createErrorDataCollector(): ErrorCollector
    {
        $dummyChangedFilesDetector = self::createDummyChangedFilesDetector();
        return new ErrorCollector(new ErrorSorter, $dummyChangedFilesDetector);
    }

    private static function createFileFromFilePath(
        string $filePath,
        ?ErrorCollector $errorDataCollector = null
    ): File {
        $fileToTokenParser = new FileToTokensParser;

        $errorDataCollector = $errorDataCollector ?: self::createErrorDataCollector();

        $tokens = $fileToTokenParser->parseFromFilePath($filePath);

        $fixer = new Fixer;
        $file = new File($filePath, $tokens, $fixer, $errorDataCollector, true);
        $file->fixer->startFile($file);

        return $file;
    }

    private static function createDummyChangedFilesDetector(): ChangedFilesDetectorInterface
    {
        return new class implements ChangedFilesDetectorInterface
        {
            public function addFile(string $filePath): void
            {
            }

            public function invalidateFile(string $filePath): void
            {
            }

            public function hasFileChanged(string $filePath): bool
            {
                return true;
            }

            public function clearCache(): void
            {
            }
        };
    }
}
