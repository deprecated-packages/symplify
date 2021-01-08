<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallOnTypeRule\Fixture;

use ReflectionProperty;

final class SkipPropertyReflection
{
    public function test(ReflectionProperty $reflectionProperty): void
    {
        $comments = $reflectionProperty->getDocComment();
    }
}
