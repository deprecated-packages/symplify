<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Contract\ValueObject;

interface FileErrorInterface
{
    public function getErrorMessage(): string;

    public function getRelativeFilePath(): string;
}
