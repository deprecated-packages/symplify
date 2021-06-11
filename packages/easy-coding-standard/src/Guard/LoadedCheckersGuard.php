<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Guard;

use Symplify\EasyCodingStandard\Application\FileProcessorCollector;
use Symplify\EasyCodingStandard\Bootstrap\NoCheckersLoaderReporter;

final class LoadedCheckersGuard
{
    public function __construct(
        private FileProcessorCollector $fileProcessorCollector,
        private NoCheckersLoaderReporter $noCheckersLoaderReporter
    ) {
    }

    public function areSomeCheckerRegistered(): bool
    {
        $checkerCount = $this->getCheckerCount();
        return $checkerCount !== 0;
    }

    public function report(): void
    {
        $this->noCheckersLoaderReporter->report();
    }

    private function getCheckerCount(): int
    {
        $checkerCount = 0;

        $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
        foreach ($fileProcessors as $fileProcessor) {
            $checkerCount += count($fileProcessor->getCheckers());
        }

        return $checkerCount;
    }
}
