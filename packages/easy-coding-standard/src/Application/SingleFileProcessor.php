<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\Skipper\Skipper\Skipper;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SingleFileProcessor
{
    public function __construct(
        private Skipper $skipper,
        private ChangedFilesDetector $changedFilesDetector,
        private FileProcessorCollector $fileProcessorCollector
    ) {
    }

    /**
     * @return array<FileDiff|CodingStandardError>
     */
    public function processFileInfo(SmartFileInfo $smartFileInfo): array
    {
        if ($this->skipper->shouldSkipFileInfo($smartFileInfo)) {
            return [];
        }

        $errorsAndDiffs = [];

        $this->changedFilesDetector->addFileInfo($smartFileInfo);
        $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
        foreach ($fileProcessors as $fileProcessor) {
            if ($fileProcessor->getCheckers() === []) {
                continue;
            }

            $currentErrorsAndFileDiffs = $fileProcessor->processFile($smartFileInfo);
            if ($currentErrorsAndFileDiffs === []) {
                continue;
            }

            $errorsAndDiffs = array_merge($errorsAndDiffs, $currentErrorsAndFileDiffs);
        }

        return $errorsAndDiffs;
    }
}
