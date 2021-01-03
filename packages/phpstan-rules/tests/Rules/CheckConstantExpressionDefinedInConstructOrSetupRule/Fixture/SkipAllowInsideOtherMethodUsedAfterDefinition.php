<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class SkipAllowInsideOtherMethodUsedAfterDefinition
{
    public function otherMethod()
    {
        $a = __DIR__ . '/static.txt';
        $this->run($a);
    }

    public function run(string $arg)
    {

    }
}
