<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Composer;

use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Location\DirectoryChecker;
use Symplify\PHPStanRules\ValueObject\ClassNamespaceAndDirectory;

final class ClassNamespaceMatcher
{
    public function __construct(
        private DirectoryChecker $directoryChecker
    ) {
    }

    /**
     * @param array<string, string|string[]> $autoloadPsr4Paths
     * @return ClassNamespaceAndDirectory[]
     */
    public function matchPossibleDirectoriesForClass(
        string $namespaceBeforeClass,
        array $autoloadPsr4Paths,
        Scope $scope
    ): array {
        $namespaceToDirectories = [];

        foreach ($autoloadPsr4Paths as $namespace => $directory) {
            $namespace = rtrim($namespace, '\\') . '\\';
            if ($namespaceBeforeClass === $namespace) {
                return [];
            }

            $nestedDirectories = $this->standardizeToArray($directory);
            foreach ($nestedDirectories as $nestedDirectory) {
                if (! $this->directoryChecker->isInDirectoryNamed($scope, $nestedDirectory)) {
                    continue;
                }

                $namespaceToDirectories[] = new ClassNamespaceAndDirectory(
                    $namespace,
                    $nestedDirectory,
                    $namespaceBeforeClass
                );
                continue 2;
            }
        }

        return $namespaceToDirectories;
    }

    /**
     * @param string|string[] $items
     * @return string[]
     */
    private function standardizeToArray(string | array $items): array
    {
        if (! is_array($items)) {
            return [$items];
        }

        return $items;
    }
}
