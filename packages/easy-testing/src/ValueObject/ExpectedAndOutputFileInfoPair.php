<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

/**
 * @api
 */
final class ExpectedAndOutputFileInfoPair
{
    public function __construct(
        private SmartFileInfo $expectedFileInfo,
        private ?SmartFileInfo $outputFileInfo
    ) {
    }

    public function getExpectedFileContent(): string
    {
        return $this->expectedFileInfo->getContents();
    }

    public function getOutputFileContent(): string
    {
        if (! $this->outputFileInfo instanceof SmartFileInfo) {
            throw new ShouldNotHappenException();
        }

        return $this->outputFileInfo->getContents();
    }

    public function doesOutputFileExist(): bool
    {
        return $this->outputFileInfo !== null;
    }
}
