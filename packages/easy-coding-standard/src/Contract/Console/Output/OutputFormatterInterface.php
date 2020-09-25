<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Console\Output;

use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;

interface OutputFormatterInterface
{
    public function report(ErrorAndDiffCollector $errorAndDiffCollector, int $processedFilesCount): int;

    public function getName(): string;
}
