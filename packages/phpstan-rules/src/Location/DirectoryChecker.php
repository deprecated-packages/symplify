<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Location;

use Nette\Utils\Strings;
use PHPStan\Analyser\Scope;

final class DirectoryChecker
{
    public function isInDirectoryNamed(Scope $scope, string $directoryName): bool
    {
        $normalized = $this->normalizePath($directoryName);
        $directoryName = rtrim($normalized, '\/');

        return Strings::contains($scope->getFile(), DIRECTORY_SEPARATOR . $directoryName . DIRECTORY_SEPARATOR);
    }

    private function normalizePath(string $directoryName): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $directoryName);
    }
}
