<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Reporter;

use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;

final class ProcessedFileReporter
{
    public function __construct(
        private Configuration $configuration,
        private OutputFormatterCollector $outputFormatterCollector,
    ) {
    }

    /**
     * @param array<SystemError|FileDiff|CodingStandardError> $errorsAndDiffs
     */
    public function report(array $errorsAndDiffs): int
    {
        $outputFormat = $this->configuration->getOutputFormat();
        $outputFormatter = $this->outputFormatterCollector->getByName($outputFormat);

        /** @var SystemError[] $systemErrors */
        $systemErrors = array_filter($errorsAndDiffs, fn (object $object) => $object instanceof SystemError);

        /** @var FileDiff[] $fileDiffs */
        $fileDiffs = array_filter($errorsAndDiffs, fn (object $object) => $object instanceof FileDiff);

        /** @var CodingStandardError[] $codingStandardErrors */
        $codingStandardErrors = array_filter(
            $errorsAndDiffs,
            fn (object $object) => $object instanceof CodingStandardError
        );

        $errorAndDiffResult = new ErrorAndDiffResult($codingStandardErrors, $fileDiffs, $systemErrors);
        return $outputFormatter->report($errorAndDiffResult);
    }
}
