<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoDuplicatedArgumentRule\Fixture;

final class SkipSimpleNumbers
{
    public function run($ret)
    {
        $this->go(1, 1);
    }

    public function go($value, $anotherValue)
    {
    }
}
