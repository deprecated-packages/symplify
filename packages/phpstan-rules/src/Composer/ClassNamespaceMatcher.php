<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Composer;

use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Location\DirectoryChecker;
use Symplify\PHPStanRules\ValueObject\ClassNamespaceAndDirectory;

final class ClassNamespaceMatcher
{
    /**
     * @var DirectoryChecker
     */
    private $directoryChecker;

    public function __construct(DirectoryChecker $directoryChecker)
    {
        $this->directoryChecker = $directoryChecker;
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

            $directories = $this->standardizeToArray($directory);
            foreach ($directories as $singleDirectory) {
                if (! $this->directoryChecker->isInDirectoryNamed($scope, $singleDirectory)) {
                    continue;
                }
            }

            $namespaceToDirectories[] = new ClassNamespaceAndDirectory(
                $namespace,
                $singleDirectory,
                $namespaceBeforeClass
            );
        }

        return $namespaceToDirectories;
    }

    /**
     * @param string|string[] $items
     * @return string[]
     */
    private function standardizeToArray($items): array
    {
        if (! is_array($items)) {
            return [$items];
        }

        return $items;
    }
}
