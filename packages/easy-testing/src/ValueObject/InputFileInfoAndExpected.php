<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class InputFileInfoAndExpected
{
    /**
     * @param mixed $expected
     */
    public function __construct(
        private SmartFileInfo $inputFileInfo,
        private $expected
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

    /**
     * @return mixed
     */
    public function getExpected()
    {
        return $this->expected;
    }
}
