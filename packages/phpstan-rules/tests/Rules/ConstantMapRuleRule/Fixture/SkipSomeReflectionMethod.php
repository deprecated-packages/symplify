<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ConstantMapRuleRule\Fixture;

use ReflectionMethod;
use Symplify\PHPStanRules\Exception\NotImplementedException;

final class SkipSomeReflectionMethod
{
    public function resolve(ReflectionMethod $reflectionMethod): string
    {
        if ($reflectionMethod->isPublic()) {
            return 'public';
        }

        if ($reflectionMethod->isProtected()) {
            return 'protected';
        }

        if ($reflectionMethod->isPrivate()) {
            return 'private';
        }

        throw new NotImplementedException();
    }
}
