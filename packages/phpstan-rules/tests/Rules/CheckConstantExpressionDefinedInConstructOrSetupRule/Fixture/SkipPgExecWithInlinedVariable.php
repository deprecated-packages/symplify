<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

final class SkipPgExecWithInlinedVariable
{
    public function run()
    {
        if (($row = pg_exec('some query'))) {
        }
    }
}
