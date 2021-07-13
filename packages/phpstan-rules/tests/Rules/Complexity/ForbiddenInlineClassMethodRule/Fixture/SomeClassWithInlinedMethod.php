<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenInlineClassMethodRule\Fixture;

class SomeClassWithInlinedMethod
{
    public function run()
    {
        return $this->away();
    }

    private function away()
    {
        return $this->exploreMethod();
    }

    private function exploreMethod(): int
    {
        return mt_rand(0, 100);
    }
}
