<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\SmartFileSystem\SmartFileInfo;

final class InputFileInfoAndExpectedFileInfo
{
    public function __construct(
        private SplFileInfo $inputFileInfo,
        private SplFileInfo $expectedFileInfo
    ) {
    }

    public function getInputFileInfo(): SplFileInfo
    {
        return $this->inputFileInfo;
    }

    public function getExpectedFileInfo(): SplFileInfo
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
