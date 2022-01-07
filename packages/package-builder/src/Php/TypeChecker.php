<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Php;

/**
 * @api
 */
final class TypeChecker
{
    /**
     * @param object|class-string $object
     * @param array<class-string> $types
     */
    public function isInstanceOf(object | string $object, array $types): bool
    {
        foreach ($types as $type) {
            if (is_a($object, $type, true)) {
                return true;
            }
        }

        return false;
    }
}
