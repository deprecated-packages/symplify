<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Contract\ValueObject;

interface TemplateErrorInterface
{
    public function getErrorMessage(): string;

    public function getRelativeFilePath(): string;
}
