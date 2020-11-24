<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Dummy;

use Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface;

final class DummySymfonyVersionFeatureGuard implements SymfonyVersionFeatureGuardInterface
{
    public function isAtLeastSymfonyVersion(float $symfonyVersion): bool
    {
        return true;
    }
}
