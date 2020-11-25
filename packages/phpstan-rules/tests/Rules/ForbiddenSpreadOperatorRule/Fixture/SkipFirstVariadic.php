<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenSpreadOperatorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenSpreadOperatorRule\Source\ClassMethodWithFirstArgumentVariadic;

final class SkipFirstVariadic
{
    public function __construct()
    {
        $classMethodWithFirstArgumentVariadic = new ClassMethodWithFirstArgumentVariadic();
        $paths = ['...'];

        $classMethodWithFirstArgumentVariadic->process(...$paths);
    }
}
