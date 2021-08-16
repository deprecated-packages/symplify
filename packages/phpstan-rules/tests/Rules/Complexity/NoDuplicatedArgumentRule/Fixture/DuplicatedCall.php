<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoDuplicatedArgumentRule\Fixture;

final class DuplicatedCall
{
    public function run()
    {
        $this->go(1000, 1000);
    }

    public function go($value, $anotherValue)
    {
    }
}
