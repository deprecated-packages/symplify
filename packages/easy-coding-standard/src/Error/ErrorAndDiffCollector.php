<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Exception\NotSniffNorFixerException;
use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ErrorAndDiffCollector
{
    /**
     * @var CodingStandardError[]
     */
    private $codingStandardErrors = [];

    /**
     * @var SystemError[]
     */
    private $systemErrors = [];

    /**
     * @var FileDiff[]
     */
    private $fileDiffs = [];

    public function __construct(
        private ChangedFilesDetector $changedFilesDetector,
        private FileDiffFactory $fileDiffFactory,
        private CurrentParentFileInfoProvider $currentParentFileInfoProvider
    ) {
    }

    /**
     * @param class-string|string $sourceClass
     */
    public function addErrorMessage(SmartFileInfo $fileInfo, int $line, string $message, string $sourceClass): void
    {
        if ($this->currentParentFileInfoProvider->provide() !== null) {
            // skip sniff errors
            return;
        }

        $this->ensureIsFixerOrChecker($sourceClass);
        $this->changedFilesDetector->invalidateFileInfo($fileInfo);

        $codingStandardError = new CodingStandardError(
            $line,
            $message,
            $sourceClass,
            $fileInfo->getRelativeFilePathFromCwd()
        );
        $this->codingStandardErrors[] = $codingStandardError;
    }

    public function addSystemErrorMessage(SmartFileInfo $smartFileInfo, int $line, string $message): void
    {
        $this->systemErrors[] = new SystemError($line, $message, $smartFileInfo->getRelativeFilePathFromCwd());
    }

    /**
     * @return CodingStandardError[]
     */
    public function getErrors(): array
    {
        return $this->codingStandardErrors;
    }

    /**
     * @return SystemError[]
     */
    public function getSystemErrors(): array
    {
        return $this->systemErrors;
    }

    /**
     * @param array<class-string<FixerInterface|\PHP_CodeSniffer\Sniffs\Sniff>|string> $appliedCheckers
     */
    public function addDiffForFileInfo(SmartFileInfo $smartFileInfo, string $diff, array $appliedCheckers): void
    {
        $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);

        foreach ($appliedCheckers as $appliedChecker) {
            $this->ensureIsFixerOrChecker($appliedChecker);
        }

        $this->fileDiffs[] = $this->fileDiffFactory->createFromDiffAndAppliedCheckers(
            $smartFileInfo,
            $diff,
            $appliedCheckers
        );
    }

    /**
     * @return FileDiff[]
     */
    public function getFileDiffs(): array
    {
        return $this->fileDiffs;
    }

    /**
     * Used by external sniff/fixer testing classes
     */
    public function resetCounters(): void
    {
        $this->codingStandardErrors = [];
        $this->fileDiffs = [];
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
