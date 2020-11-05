<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

final class Multiplex
{
    public function run()
    {
        $value = __DIR__ . getcwd();

        $value2 = __DIR__;
    }
}
