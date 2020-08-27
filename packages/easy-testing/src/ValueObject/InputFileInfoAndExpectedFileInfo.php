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
    private $expectedFilenfo;

    public function __construct(SmartFileInfo $inputFileInfo, SmartFileInfo $expectedFilenfo)
    {
        $this->inputFileInfo = $inputFileInfo;
        $this->expectedFilenfo = $expectedFilenfo;
    }

    public function getInputFileInfo(): SmartFileInfo
    {
        return $this->inputFileInfo;
    }

    public function getExpectedFilenfo(): SmartFileInfo
    {
        return $this->expectedFilenfo;
    }

    public function getExpectedFilenfoRealPath(): string
    {
        return $this->expectedFilenfo->getRealPath();
    }
}
