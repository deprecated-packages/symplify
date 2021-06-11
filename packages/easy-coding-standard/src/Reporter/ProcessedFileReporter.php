<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Reporter;

use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffResultFactory;

final class ProcessedFileReporter
{
    public function __construct(
        private Configuration $configuration,
        private OutputFormatterCollector $outputFormatterCollector,
        private ErrorAndDiffResultFactory $errorAndDiffResultFactory
    ) {
    }

    public function report(int $processedFileCount): int
    {
        $outputFormat = $this->configuration->getOutputFormat();
        $outputFormatter = $this->outputFormatterCollector->getByName($outputFormat);

        $errorAndDiffResult = $this->errorAndDiffResultFactory->create();
        return $outputFormatter->report($errorAndDiffResult, $processedFileCount);
    }
}
