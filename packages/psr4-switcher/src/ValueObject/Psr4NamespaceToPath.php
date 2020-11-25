<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\ValueObject;

final class Psr4NamespaceToPath
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $path;

    public function __construct(string $namespace, string $path)
    {
        $this->namespace = $namespace;
        $this->path = $path;
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
