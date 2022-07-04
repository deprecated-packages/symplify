<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @api
 */
final class InputFileInfoAndExpectedFileInfo
{
    public function __construct(
        private SmartFileInfo $inputFileInfo,
        private SmartFileInfo $expectedFileInfo
    ) {
    }

    public function getInputFileInfo(): SmartFileInfo
    {
        return $this->inputFileInfo;
    }

    public function getExpectedFileInfo(): SmartFileInfo
    {
        return $this->expectedFileInfo;
    }

    public function getExpectedFileContent(): string
    {
        return $this->expectedFileInfo->getContents();
    }

    public function getExpectedFileInfoRealPath(): string
    {
        return $this->expectedFileInfo->getRealPath();
    }
}
