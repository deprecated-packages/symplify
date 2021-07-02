<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenArrayMethodCallRule\Fixture;

final class ReturnCallable
{
    public function run()
    {
        return [$this, 'two'];
    }

    public function two()
    {
    }
}
