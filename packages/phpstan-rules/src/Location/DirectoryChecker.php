<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Location;

use PHPStan\Analyser\Scope;

final class DirectoryChecker
{
    /**
     * @param string[] $directoryNames
     */
    public function isInDirectoryNames(Scope $scope, array $directoryNames): bool
    {
        foreach ($directoryNames as $directoryName) {
            if ($this->isInDirectoryNamed($scope, $directoryName)) {
                return true;
            }
        }

        return false;
    }

    public function isInDirectoryNamed(Scope $scope, string $directoryName): bool
    {
        $normalized = $this->normalizePath($directoryName);
        $directoryName = rtrim($normalized, '\/');

        return \str_contains($scope->getFile(), DIRECTORY_SEPARATOR . $directoryName . DIRECTORY_SEPARATOR);
    }

    private function normalizePath(string $directoryName): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $directoryName);
    }
}
