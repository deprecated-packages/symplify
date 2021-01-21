<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Composer;

use Nette\Utils\Strings;

final class Psr4PathValidator
{
    public function isClassNamespaceCorrect(
        string $namespace,
        string $directory,
        string $namespaceBeforeClass,
        string $file
    ): bool {
        /** @var array<int, string> $paths */
        $paths = explode($directory, $file);
        if (count($paths) === 1) {
            return false;
        }

        $directoryInNamespacedRoot = dirname($paths[1]);
        $directoryInNamespacedRoot = $this->normalizePath($directoryInNamespacedRoot);

        $namespaceSuffixByDirectoryClass = ltrim($directoryInNamespacedRoot, '\\');

        $namespaceSuffixByNamespaceBeforeClass = rtrim(
            Strings::substring($namespaceBeforeClass, strlen($namespace)),
            '\\'
        );

        return $namespaceSuffixByDirectoryClass === $namespaceSuffixByNamespaceBeforeClass;
    }

    private function normalizePath(string $path): string
    {
        return str_replace('/', '\\', $path);
    }
}
