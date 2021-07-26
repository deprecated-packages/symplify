<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Composer;

use Nette\Utils\Strings;
use Symplify\PHPStanRules\ValueObject\ClassNamespaceAndDirectory;

/**
 * @see \Symplify\PHPStanRules\Tests\Composer\Psr4PathValidatorTest
 */
final class Psr4PathValidator
{
    public function isClassNamespaceCorrect(
        ClassNamespaceAndDirectory $classNamespaceAndDirectory,
        string $file
    ): bool {
        $singleDirectory = rtrim($classNamespaceAndDirectory->getSingleDirectory(), '/');
        $splitPaths = Strings::split($file, '#\/' . preg_quote($singleDirectory, '#') . '\/#');

        if (count($splitPaths) === 1) {
            return false;
        }

        $directoryInNamespacedRoot = dirname($splitPaths[1]);
        $normalizedDirectoryInNamespacedRoot = $this->normalizePath($directoryInNamespacedRoot);

        $namespaceSuffixByDirectoryClass = ltrim($normalizedDirectoryInNamespacedRoot, '\\');

        // @todo put into value object
        $namespaceSuffixByNamespaceBeforeClass = rtrim(
            Strings::substring(
                $classNamespaceAndDirectory->getNamespaceBeforeClass(),
                strlen($classNamespaceAndDirectory->getNamespace())
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
