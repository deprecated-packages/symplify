<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Reporter;

use Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;

final class ProcessedFileReporter
{
    public function __construct(
        private OutputFormatterCollector $outputFormatterCollector,
    ) {
    }

    /**
     * @param array<string, array<SystemError|FileDiff|CodingStandardError>> $errorsAndDiffs
     */
    public function report(array $errorsAndDiffs, Configuration $configuration): int
    {
        $outputFormat = $configuration->getOutputFormat();
        $outputFormatter = $this->outputFormatterCollector->getByName($outputFormat);

        /** @var SystemError[] $systemErrors */
        $systemErrors = $errorsAndDiffs['system_errors'] ?? [];

        /** @var FileDiff[] $fileDiffs */
        $fileDiffs = $errorsAndDiffs['file_diffs'] ?? [];

        /** @var CodingStandardError[] $codingStandardErrors */
        $codingStandardErrors = $errorsAndDiffs['coding_standard_errors'] ?? [];

        $errorAndDiffResult = new ErrorAndDiffResult($codingStandardErrors, $fileDiffs, $systemErrors);

        return $outputFormatter->report($errorAndDiffResult, $configuration);
    }
}
