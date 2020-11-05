<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Reflection;

final class ClassLikeExistenceChecker
{
    public function doesClassLikeExist(string $classLike): bool
    {
        if (class_exists($classLike)) {
            return true;
        }

        if (interface_exists($classLike)) {
            return true;
        }

        return trait_exists($classLike);
    }
}
