<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoDuplicatedArgumentRule\Fixture;

final class DuplicatedCallWithArray
{
    public function run()
    {
        $this->go('hey', ['hey']);
    }

    public function go($value, $anotherValue)
    {
    }
}
