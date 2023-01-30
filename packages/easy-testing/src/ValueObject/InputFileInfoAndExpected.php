<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @api
 */
final class InputFileInfoAndExpected
{
    public function __construct(
        private readonly SmartFileInfo $inputFileInfo,
        private readonly mixed $expected
    ) {
    }

    public function getInputFileContent(): string
    {
        return $this->inputFileInfo->getContents();
    }

    public function getInputFileInfo(): SmartFileInfo
    {
        return $this->inputFileInfo;
    }

    public function getInputFileRealPath(): string
    {
        return $this->inputFileInfo->getRealPath();
    }

    public function getExpected(): mixed
    {
        return $this->expected;
    }
}
