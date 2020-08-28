<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class InputFileInfoAndExpectedFileInfo
{
    /**
     * @var SmartFileInfo
     */
    private $inputFileInfo;

    /**
     * @var SmartFileInfo
     */
    private $expectedFileInfo;

    public function __construct(SmartFileInfo $inputFileInfo, SmartFileInfo $expectedFilenfo)
    {
        $this->inputFileInfo = $inputFileInfo;
        $this->expectedFileInfo = $expectedFilenfo;
    }

    public function getInputFileInfo(): SmartFileInfo
    {
        return $this->inputFileInfo;
    }

    public function getExpectedFileInfo(): SmartFileInfo
    {
        return $this->expectedFileInfo;
    }

    public function getExpectedFilenfoRealPath(): string
    {
        return $this->expectedFileInfo->getRealPath();
    }
}
