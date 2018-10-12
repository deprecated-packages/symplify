<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Types;

final class ClassLikeExistenceChecker
{
    public function exists(string $type): bool
    {
        return class_exists($type) || interface_exists($type) || trait_exists($type);
    }
}
