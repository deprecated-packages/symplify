<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Exception\NotSniffNorFixerException;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ErrorAndDiffCollector
{
    public function __construct(
        private ChangedFilesDetector $changedFilesDetector,
    ) {
    }

    /**
     * @param class-string<FixerInterface|Sniff>|string $sourceClass
     */
    public function addErrorMessage(SmartFileInfo $fileInfo, int $line, string $message, string $sourceClass): void
    {
        $this->ensureIsFixerOrChecker($sourceClass);
        $this->changedFilesDetector->invalidateFileInfo($fileInfo);
    }

    /**
     * @param class-string|string $sourceClass
     */
    private function ensureIsFixerOrChecker(string $sourceClass): void
    {
        // remove dot suffix of "."
        if (\str_contains($sourceClass, '.')) {
            $sourceClass = (string) Strings::before($sourceClass, '.');
        }

        if (is_a($sourceClass, FixerInterface::class, true)) {
            return;
        }

        if (is_a($sourceClass, Sniff::class, true)) {
            return;
        }

        $message = sprintf(
            'Source class "%s" must be "%s" or "%s"',
            $sourceClass,
            FixerInterface::class,
            Sniff::class
        );
        throw new NotSniffNorFixerException($message);
    }
}
