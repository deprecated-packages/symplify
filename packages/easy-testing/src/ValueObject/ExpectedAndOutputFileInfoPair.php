<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ExpectedAndOutputFileInfoPair
{
    public function __construct(
        private SmartFileInfo $expectedFileInfo,
        private ?SmartFileInfo $outputFileInfo
    ) {
    }

    /**
     * @noRector \Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector
     */
    public function getExpectedFileContent(): string
    {
        return $this->expectedFileInfo->getContents();
    }

    /**
     * @noRector \Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector
     */
    public function getOutputFileContent(): string
    {
        if (! $this->outputFileInfo instanceof SmartFileInfo) {
            throw new ShouldNotHappenException();
        }

        return $this->outputFileInfo->getContents();
    }

    /**
     * @noRector \Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector
     */
    public function doesOutputFileExist(): bool
    {
        return $this->outputFileInfo !== null;
    }
}
