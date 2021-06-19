<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Application;

use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\SmartFileSystem\SmartFileInfo;

interface FileProcessorInterface
{
    public function processFileToString(SmartFileInfo $smartFileInfo): string;

    /**
     * @return array<FileDiff|CodingStandardError>
     */
    public function processFile(SmartFileInfo $smartFileInfo): array;

    /**
     * @return object[]
     */
    public function getCheckers(): array;
}
