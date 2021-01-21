<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Composer;

use Nette\Utils\Strings;
use Symplify\PHPStanRules\ValueObject\ClassNamespaceAndDirectory;

final class Psr4PathValidator
{
    public function isClassNamespaceCorrect(
        ClassNamespaceAndDirectory $classNamespaceAndDirectories,
        string $file
    ): bool {
        /** @var array<int, string> $paths */
        $paths = explode($classNamespaceAndDirectories->getSingleDirectory(), $file);
        if (count($paths) === 1) {
            return false;
        }

        $directoryInNamespacedRoot = dirname($paths[1]);
        $directoryInNamespacedRoot = $this->normalizePath($directoryInNamespacedRoot);

        $namespaceSuffixByDirectoryClass = ltrim($directoryInNamespacedRoot, '\\');

        // @todo put into value object
        $namespaceSuffixByNamespaceBeforeClass = rtrim(
            Strings::substring(
                $classNamespaceAndDirectories->getNamespaceBeforeClass(),
                strlen($classNamespaceAndDirectories->getNamespace())
            ),
            '\\'
        );

        return $namespaceSuffixByDirectoryClass === $namespaceSuffixByNamespaceBeforeClass;
    }

    private function normalizePath(string $path): string
    {
        return str_replace('/', '\\', $path);
    }
}
