<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenInlineClassMethodRule\Fixture;

final class SkipNoMethodCall
{
    public function run()
    {
        return $this->away();
    }

    private function away()
    {
        return mt_rand(0, 100) + 100;
    }
}
