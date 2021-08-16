<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoDuplicatedArgumentRule\Fixture;

final class SkipDifferentValues
{
    public function run()
    {
        $this->go(1, 100);
    }

    public function go($value, $anotherValue)
    {
    }
}
