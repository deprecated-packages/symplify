<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Nette\Utils\Arrays;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Compiler\Exception\ShouldNotHappenException;
use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ErrorAndDiffCollector
{
    /**
     * @var array<string, CodingStandardError[]>
     */
    private $codingStandardErrors = [];

    /**
     * @var array<string, FileDiff[]>
     */
    private $fileDiffs = [];

    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

    /**
     * @var ErrorSorter
     */
    private $errorSorter;

    /**
     * @var FileDiffFactory
     */
    private $fileDiffFactory;

    /**
     * @var ErrorFactory
     */
    private $errorFactory;

    /**
     * @var CurrentParentFileInfoProvider
     */
    private $currentParentFileInfoProvider;

    public function __construct(
        ChangedFilesDetector $changedFilesDetector,
        ErrorSorter $errorSorter,
        FileDiffFactory $fileDiffFactory,
        ErrorFactory $errorFactory,
        CurrentParentFileInfoProvider $currentParentFileInfoProvider
    ) {
        $this->changedFilesDetector = $changedFilesDetector;
        $this->errorSorter = $errorSorter;
        $this->fileDiffFactory = $fileDiffFactory;
        $this->errorFactory = $errorFactory;
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
    }

    /**
     * @param class-string $sourceClass
     */
    public function addErrorMessage(SmartFileInfo $fileInfo, int $line, string $message, string $sourceClass): void
    {
        if ($this->currentParentFileInfoProvider->provide() !== null) {
            // skip sniff errors
            return;
        }

        $this->ensureIsFixerOrChecker($sourceClass);
        $this->changedFilesDetector->invalidateFileInfo($fileInfo);

        $relativeFilePathFromCwd = $fileInfo->getRelativeFilePathFromCwd();

        $codingStandardError = $this->errorFactory->create($line, $message, $sourceClass, $fileInfo);
        $this->codingStandardErrors[$relativeFilePathFromCwd][] = $codingStandardError;
    }

    /**
     * @return CodingStandardError[][]
     */
    public function getErrors(): array
    {
        return $this->errorSorter->sortByFileAndLine($this->codingStandardErrors);
    }

    public function getErrorCount(): int
    {
        return count(Arrays::flatten($this->codingStandardErrors));
    }

    /**
     * @param class-string[] $appliedCheckers
     */
    public function addDiffForFileInfo(SmartFileInfo $smartFileInfo, string $diff, array $appliedCheckers): void
    {
        $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);

        foreach ($appliedCheckers as $appliedChecker) {
            $this->ensureIsFixerOrChecker($appliedChecker);
        }

        $this->fileDiffs[$smartFileInfo->getRelativeFilePath()][] = $this->fileDiffFactory->createFromDiffAndAppliedCheckers(
            $diff,
            $appliedCheckers
        );
    }

    public function getFileDiffsCount(): int
    {
        return count(Arrays::flatten($this->getFileDiffs()));
    }

    /**
     * @return FileDiff[][]
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

    private function ensureIsFixerOrChecker(string $sourceClass): void
    {
        if (is_a($sourceClass, FixerInterface::class, true)) {
            return;
        }

        if (is_a($sourceClass, Sniff::class, true)) {
            return;
        }

        $message = sprintf('Source class "%s" must be "%s" or "%s"', $sourceClass, FixerInterface::class, Sniff::class);
        throw new ShouldNotHappenException($message);
    }
}
