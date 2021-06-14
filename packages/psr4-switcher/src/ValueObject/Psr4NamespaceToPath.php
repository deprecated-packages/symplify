<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\ValueObject;

use Stringable;

final class Psr4NamespaceToPath implements Stringable
{
    public function __construct(
        private string $namespace,
        private string $path
    ) {
    }

    /**
     * For array_unique()
     */
    public function __toString(): string
    {
        return $this->namespace . $this->path;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
