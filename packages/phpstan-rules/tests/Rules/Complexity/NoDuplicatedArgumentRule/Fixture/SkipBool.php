<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoDuplicatedArgumentRule\Fixture;

final class SkipBool
{
    public function run()
    {
        $this->go(false, false);
    }

    public function go($value, $anotherValue)
    {
    }
}
