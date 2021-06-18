<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use ParseError;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use Symplify\Skipper\Skipper\Skipper;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SingleFileProcessor
{
    public function __construct(
        private Skipper $skipper,
        private ChangedFilesDetector $changedFilesDetector,
        private ErrorAndDiffCollector $errorAndDiffCollector,
        private FileProcessorCollector $fileProcessorCollector
    ) {
    }

    /**
     * @return array<SystemError|FileDiff|CodingStandardError>
     */
    public function processFileInfo(SmartFileInfo $smartFileInfo): array
    {
        if ($this->skipper->shouldSkipFileInfo($smartFileInfo)) {
            return [];
        }

        $errorsAndDiffs = [];
        // @todo get rid of this service approach and return array of errors/diffs directly

        $this->errorAndDiffCollector->resetCounters();

        try {
            $this->changedFilesDetector->addFileInfo($smartFileInfo);
            $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
            foreach ($fileProcessors as $fileProcessor) {
                if ($fileProcessor->getCheckers() === []) {
                    continue;
                }

                $fileProcessor->processFile($smartFileInfo);
            }
        } catch (ParseError $parseError) {
            $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
            $errorsAndDiffs[] = new SystemError(
                $parseError->getLine(),
                $parseError->getMessage(),
                $smartFileInfo->getRelativeFilePathFromCwd()
            );
        }

        $errorsAndDiffs = array_merge(
            $errorsAndDiffs,
            $this->errorAndDiffCollector->getErrors(),
            $this->errorAndDiffCollector->getFileDiffs()
        );

        return $errorsAndDiffs;
    }
}
