<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject;

use Symfony\Component\Finder\SplFileInfo;

final class InputFileInfoAndExpected
{
    /**
     * @param mixed $expected
     */
    public function __construct(
        private SplFileInfo $inputFileInfo,
        private $expected
    ) {
    }

    public function getInputFileContent(): string
    {
        return $this->inputFileInfo->getContents();
    }

    public function getInputFileInfo(): SplFileInfo
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
