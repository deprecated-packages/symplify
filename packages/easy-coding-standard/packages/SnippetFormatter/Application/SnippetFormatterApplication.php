<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Application;

use PhpCsFixer\Differ\DifferInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter;
use Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetKind;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class SnippetFormatterApplication
{
    public function __construct(
        private SnippetReporter $snippetReporter,
        private SnippetFormatter $snippetFormatter,
        private SmartFileSystem $smartFileSystem,
        private SymfonyStyle $symfonyStyle,
        private ProcessedFileReporter $processedFileReporter,
        private DifferInterface $differ,
        private ColorConsoleDiffFormatter $colorConsoleDiffFormatter
    ) {
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @param SnippetPattern::* $snippetPattern
     * @param SnippetKind::* $kind
     */
    public function processFileInfosWithSnippetPattern(
        Configuration $configuration,
        array $fileInfos,
        string $snippetPattern,
        string $kind
    ): int {
        $sources = $configuration->getSources();

        $fileCount = count($fileInfos);
        if ($fileCount === 0) {
            $this->snippetReporter->reportNoFilesFound($sources);
            return Command::SUCCESS;
        }

        $this->symfonyStyle->progressStart($fileCount);

        $errorsAndDiffs = [];

        foreach ($fileInfos as $fileInfo) {
            $fileDiff = $this->processFileInfoWithPattern($fileInfo, $snippetPattern, $kind, $configuration);
            if ($fileDiff instanceof FileDiff) {
                $errorsAndDiffs[Bridge::FILE_DIFFS][] = $fileDiff;
            }

            $this->symfonyStyle->progressAdvance();
        }

        return $this->processedFileReporter->report($errorsAndDiffs, $configuration);
    }

    /**
     * @param SnippetPattern::* $snippetPattern
     * @param SnippetKind::* $kind
     */
    private function processFileInfoWithPattern(
        SmartFileInfo $phpFileInfo,
        string $snippetPattern,
        string $kind,
        Configuration $configuration
    ): ?FileDiff {
        $fixedContent = $this->snippetFormatter->format($phpFileInfo, $snippetPattern, $kind, $configuration);

        $originalContent = $phpFileInfo->getContents();
        if ($phpFileInfo->getContents() === $fixedContent) {
            // nothing has changed
            return null;
        }

        if (! $configuration->isFixer()) {
            return null;
        }

        $this->smartFileSystem->dumpFile($phpFileInfo->getPathname(), $fixedContent);

        $diff = $this->differ->diff($originalContent, $fixedContent);
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);

        return new FileDiff(
            $phpFileInfo->getRelativeFilePathFromCwd(),
            $diff,
            $consoleFormattedDiff,
            // @todo
            []
        );
    }
}
