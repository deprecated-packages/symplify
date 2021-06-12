<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\ValueObject;

final class Psr4NamespaceToPaths
{
    /**
     * @var string[]
     */
    private array $paths = [];

    /**
     * @param string[] $paths
     */
    public function __construct(
        private string $namespace,
        array $paths
    ) {
        $this->paths = $paths;
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
