<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class ClassNamespaceAndDirectory
{
    public function __construct(
        private string $namespace,
        private string $directory,
        private string $namespaceBeforeClass
    ) {
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getSingleDirectory(): string
    {
        return $this->directory;
    }

    public function getNamespaceBeforeClass(): string
    {
        return $this->namespaceBeforeClass;
    }
}
