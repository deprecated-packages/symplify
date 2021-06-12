<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\ValueObject;

final class RuleClassWithFilePath
{
    public function __construct(
        private string $class,
        private string $path
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
