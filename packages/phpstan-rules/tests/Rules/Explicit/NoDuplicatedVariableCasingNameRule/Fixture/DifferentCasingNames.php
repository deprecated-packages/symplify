<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoDuplicatedVariableCasingNameRule\Fixture;

final class DifferentCasingNames
{
    public function run()
    {
        $value = 1000;

        $valUE = 2000;
    }
}
