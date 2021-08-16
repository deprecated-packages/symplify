<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoDuplicatedArgumentRule\Fixture;

final class SkipArrayCombine
{
    public function run()
    {
        $values = [100];
        return \array_combine($values, $values);
    }
}
