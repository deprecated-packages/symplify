<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class LatteError
{
    public function __construct(
        private string $errorMessage,
        private SmartFileInfo $smartFileInfo
    ) {
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getRelativeFilePath(): string
    {
        return $this->smartFileInfo->getRelativeFilePath();
    }
}
