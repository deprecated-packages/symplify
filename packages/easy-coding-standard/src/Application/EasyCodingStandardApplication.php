<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use ParseError;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FileSystem\FileFilter;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\EasyCodingStandard\Parallel\Application\ParallelFileProcessor;
use Symplify\EasyCodingStandard\Parallel\CpuCoreCountProvider;
use Symplify\EasyCodingStandard\Parallel\FileSystem\FilePathNormalizer;
use Symplify\EasyCodingStandard\Parallel\Scheduler;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use Symplify\SmartFileSystem\SmartFileInfo;

final class EasyCodingStandardApplication
{
    /**
     * @var string
     */
    private const ARGV = 'argv';

    public function __construct(
        private EasyCodingStandardStyle $easyCodingStandardStyle,
        private SourceFinder $sourceFinder,
        private ChangedFilesDetector $changedFilesDetector,
        private FileFilter $fileFilter,
        private SingleFileProcessor $singleFileProcessor,
        private Scheduler $scheduler,
        private ParallelFileProcessor $parallelFileProcessor,
        private CpuCoreCountProvider $cpuCoreCountProvider,
        private SymfonyStyle $symfonyStyle,
        private FilePathNormalizer $filePathNormalizer
    ) {
    }

    /**
     * @return array<string, array<SystemError|FileDiff|CodingStandardError>>
     */
    public function run(Configuration $configuration, InputInterface $input): array
    {
        // 1. find files in sources
        $fileInfos = $this->sourceFinder->find($configuration->getSources(), $configuration->doesMatchGitDiff());

        // 2. clear cache
        if ($configuration->shouldClearCache()) {
            $this->changedFilesDetector->clearCache();
        } else {
            $fileInfos = $this->fileFilter->filterOnlyChangedFiles($fileInfos);
        }

        // no files found
        $filesCount = count($fileInfos);
        if ($filesCount === 0) {
            return [];
        }

        if ($configuration->isParallel()) {
            // must be a string, otherwise the serialization returns empty arrays
            $filePaths = $this->filePathNormalizer->resolveFilePathsFromFileInfos($fileInfos);

            $schedule = $this->scheduler->scheduleWork(
                $this->cpuCoreCountProvider->provide(),
                jobSize: 20,
                files: $filePaths
            );

            // for progress bar
            $progressStarted = false;
            $postFileCallback = function (int $stepCount) use (&$progressStarted, $filePaths): void {
                if (! $progressStarted) {
                    $fileCount = count($filePaths);
                    $this->symfonyStyle->progressStart($fileCount);
                    $progressStarted = true;
                }

                $this->symfonyStyle->progressAdvance($stepCount);
                // running in paraller here → nothing else to do
            };

            $mainScript = $this->resolveCalledEcsBinary();
            if ($mainScript !== null) {
                // mimics see https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92#diff-387b8f04e0db7a06678eb52ce0c0d0aff73e0d7d8fc5df834d0a5fbec198e5daR139
                $parallelErrorsAndFileDiffs = $this->parallelFileProcessor->analyse(
                    $schedule,
                    $mainScript,
                    $postFileCallback,
                    $configuration->getConfig(),
                    $input
                );

                // @todo what exactly should be returned here?
                return $parallelErrorsAndFileDiffs;
            }
        }

        // fallback to normal process

        // process found files by each processors
        return $this->processFoundFiles($fileInfos, $configuration);
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return array<string, array<SystemError|FileDiff|CodingStandardError>>
     */
    private function processFoundFiles(array $fileInfos, Configuration $configuration): array
    {
        $fileInfoCount = count($fileInfos);

        // 3. start progress bar
        $this->outputProgressBarAndDebugInfo($fileInfoCount, $configuration);

        $errorsAndDiffs = [];

        foreach ($fileInfos as $fileInfo) {
            if ($this->easyCodingStandardStyle->isDebug()) {
                $this->easyCodingStandardStyle->writeln(' [file] ' . $fileInfo->getRelativeFilePathFromCwd());
            }

            try {
                $currentErrorsAndDiffs = $this->singleFileProcessor->processFileInfo($fileInfo, $configuration);
                if ($currentErrorsAndDiffs !== []) {
                    $this->changedFilesDetector->invalidateFileInfo($fileInfo);
                    $errorsAndDiffs = array_merge($errorsAndDiffs, $currentErrorsAndDiffs);
                }
            } catch (ParseError $parseError) {
                $this->changedFilesDetector->invalidateFileInfo($fileInfo);
                $errorsAndDiffs[Bridge::SYSTEM_ERRORS][] = new SystemError(
                    $parseError->getLine(),
                    $parseError->getMessage(),
                    $fileInfo->getRelativeFilePathFromCwd()
                );
            }

            if ($this->easyCodingStandardStyle->isDebug()) {
                continue;
            }

            if ($configuration->shouldShowProgressBar()) {
                $this->easyCodingStandardStyle->progressAdvance();
            }
        }

        return $errorsAndDiffs;
    }

    private function outputProgressBarAndDebugInfo(int $fileInfoCount, Configuration $configuration): void
    {
        if ($configuration->shouldShowProgressBar() && ! $this->easyCodingStandardStyle->isDebug()) {
            $this->easyCodingStandardStyle->progressStart($fileInfoCount);

            // show more data on progress bar
            if ($this->easyCodingStandardStyle->isVerbose()) {
                $this->easyCodingStandardStyle->enableDebugProgressBar();
            }
        }
    }

    /**
     * Path to called "ecs" binary file, e.g. "vendor/bin/ecs" returns "vendor/bin/ecs" This is needed to re-call the
     * ecs binary in sub-process in the same location.
     */
    private function resolveCalledEcsBinary(): ?string
    {
        if (! isset($_SERVER[self::ARGV][0])) {
            return null;
        }
        if (! file_exists($_SERVER[self::ARGV][0])) {
            return null;
        }
        return $_SERVER[self::ARGV][0];
    }
}
