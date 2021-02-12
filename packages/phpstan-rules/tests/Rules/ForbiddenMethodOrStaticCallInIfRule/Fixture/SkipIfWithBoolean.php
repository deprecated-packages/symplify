<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInIfRule\Fixture;

final class SkipIfWithBoolean
{
    public function run($value)
    {
        if  ($this->isValid($value)) {
            return true;
        }
    }

    private function isValid(): bool
    {
        return (bool) mt_rand(0, 100);
    }
}
