<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

trait SomeTrait
{
    public function anotherMethod()
    {
        if (true) {
            return '1';
        }

        return '2';
    }
}
