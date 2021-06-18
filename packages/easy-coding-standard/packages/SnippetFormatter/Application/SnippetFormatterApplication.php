<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Application;

use PhpCsFixer\Differ\DifferInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Reporter\ProcessedFileReporter;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter;
use Symplify\EasyCodingStandard\SnippetFormatter\Reporter\SnippetReporter;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class SnippetFormatterApplication
{
    public function __construct(
        private Configuration $configuration,
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
            return ShellCode::SUCCESS;
        }

        $this->symfonyStyle->progressStart($fileCount);

        $errorsAndDiffs = [];

        foreach ($fileInfos as $fileInfo) {
            $errorsAndDiffs = array_merge(
                $errorsAndDiffs,
                $this->processFileInfoWithPattern($fileInfo, $snippetPattern, $kind)
            );
            $this->symfonyStyle->progressAdvance();
        }

        return $this->processedFileReporter->report($errorsAndDiffs);
    }

    /**
     * @return array<SystemError|FileDiff|CodingStandardError>
     */
    private function processFileInfoWithPattern(SmartFileInfo $phpFileInfo, string $snippetPattern, string $kind): array
    {
        $fixedContent = $this->snippetFormatter->format($phpFileInfo, $snippetPattern, $kind);

        $originalContent = $phpFileInfo->getContents();
        if ($phpFileInfo->getContents() === $fixedContent) {
            // nothing has changed
            return [];
        }

        if (! $this->configuration->isFixer()) {
            return [];
        }

        $this->smartFileSystem->dumpFile($phpFileInfo->getPathname(), $fixedContent);

        $diff = $this->differ->diff($originalContent, $fixedContent);
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);

        $fileDiff = new FileDiff(
            $phpFileInfo->getRelativeFilePathFromCwd(),
            $diff,
            $consoleFormattedDiff,
            // @todo
            []
        );

        return [$fileDiff];
    }
}
