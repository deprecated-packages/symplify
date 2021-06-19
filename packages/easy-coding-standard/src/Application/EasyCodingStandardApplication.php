<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use ParseError;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FileSystem\FileFilter;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\EasyCodingStandard\Parallel\Application\ParallelFileProcessor;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use Symplify\SmartFileSystem\SmartFileInfo;

final class EasyCodingStandardApplication
{
    public function __construct(
        private EasyCodingStandardStyle $easyCodingStandardStyle,
        private SourceFinder $sourceFinder,
        private ChangedFilesDetector $changedFilesDetector,
        private Configuration $configuration,
        private FileFilter $fileFilter,
        private SingleFileProcessor $singleFileProcessor,
        private ParallelFileProcessor $parallelFileProcessor,
    ) {
    }

    /**
     * @return array<SystemError|FileDiff|CodingStandardError>
     */
    public function run(): array
    {
        // 1. find files in sources
        $fileInfos = $this->sourceFinder->find(
            $this->configuration->getSources(),
            $this->configuration->doesMatchGitDiff()
        );

        // 2. clear cache
        if ($this->configuration->shouldClearCache()) {
            $this->changedFilesDetector->clearCache();
        } else {
            $fileInfos = $this->fileFilter->filterOnlyChangedFiles($fileInfos);
        }

        // no files found
        $filesCount = count($fileInfos);
        if ($filesCount === 0) {
            return [];
        }

        // process found files by each processors
        return $this->processFoundFiles($fileInfos);
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return array<SystemError|FileDiff|CodingStandardError>
     */
    private function processFoundFiles(array $fileInfos): array
    {
        $fileInfoCount = count($fileInfos);

        // 3. start progress bar
        $this->outputProgressBarAndDebugInfo($fileInfoCount);

        $errorsAndDiffs = [];
        foreach ($fileInfos as $fileInfo) {
            if ($this->easyCodingStandardStyle->isDebug()) {
                $this->easyCodingStandardStyle->writeln(' [file] ' . $fileInfo->getRelativeFilePathFromCwd());
            }

            try {
                $currentErrorsAndDiffs = $this->singleFileProcessor->processFileInfo($fileInfo);
                if ($currentErrorsAndDiffs !== []) {
                    $this->changedFilesDetector->invalidateFileInfo($fileInfo);
                }

                $errorsAndDiffs = array_merge($errorsAndDiffs, $currentErrorsAndDiffs);
            } catch (ParseError $parseError) {
                $this->changedFilesDetector->invalidateFileInfo($fileInfo);
                $errorsAndDiffs['system_errors'][] = new SystemError(
                    $parseError->getLine(),
                    $parseError->getMessage(),
                    $fileInfo->getRelativeFilePathFromCwd()
                );
            }

            if ($this->easyCodingStandardStyle->isDebug()) {
                continue;
            }

            if ($this->configuration->shouldShowProgressBar()) {
                $this->easyCodingStandardStyle->progressAdvance();
            }
        }

        return $errorsAndDiffs;
    }

    private function outputProgressBarAndDebugInfo(int $fileInfoCount): void
    {
        if ($this->configuration->shouldShowProgressBar() && ! $this->easyCodingStandardStyle->isDebug()) {
            $this->easyCodingStandardStyle->progressStart($fileInfoCount);

            // show more data on progress bar
            if ($this->easyCodingStandardStyle->isVerbose()) {
                $this->easyCodingStandardStyle->enableDebugProgressBar();
            }
        }
    }
}
