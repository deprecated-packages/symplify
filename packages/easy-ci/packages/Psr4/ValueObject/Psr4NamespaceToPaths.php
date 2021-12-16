<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Psr4\ValueObject;

final class Psr4NamespaceToPaths
{
    /**
     * @param string[] $paths
     */
    public function __construct(
        private string $namespace,
        private array $paths
    ) {
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string[]
     */
    public function getPaths(): array
    {
        return $this->paths;
    }
}
