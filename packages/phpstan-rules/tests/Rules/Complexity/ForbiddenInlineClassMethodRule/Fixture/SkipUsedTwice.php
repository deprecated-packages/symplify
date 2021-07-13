<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenInlineClassMethodRule\Fixture;

final class SkipUsedTwice
{
    public function run()
    {
        return $this->away();
    }

    public function run2()
    {
        return $this->away();
    }

    private function away()
    {
        return mt_rand(0, 100);
    }
}
