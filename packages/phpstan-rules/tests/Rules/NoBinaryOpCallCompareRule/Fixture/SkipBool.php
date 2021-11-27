<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoBinaryOpCallCompareRule\Fixture;

final class SkipBool
{
    public function run()
    {
        if ($this->getBool() === false) {
            return 'no';
        }

        return 'yes';
    }

    private function getBool(): bool
    {
        return (bool) mt_rand(0, 1);
    }
}
