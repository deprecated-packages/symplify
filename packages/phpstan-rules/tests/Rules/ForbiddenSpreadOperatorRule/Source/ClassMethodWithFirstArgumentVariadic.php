<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenSpreadOperatorRule\Source;

final class ClassMethodWithFirstArgumentVariadic
{
    public function process(... $items)
    {
    }
}
