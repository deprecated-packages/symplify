<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Latte\ValueObject;

use Symfony\Component\Finder\SplFileInfo;

final class LatteError
{
    public function __construct(
        private string $errorMessage,
        private SplFileInfo $smartFileInfo
    ) {
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getRelativeFilePath(): string
    {
        return $this->smartFileInfo->getRelativePathname();
    }
}
