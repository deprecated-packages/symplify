<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Source\AnotherParentClassWithParams;

final class OverrideWithSameParamsAndArgs extends AnotherParentClassWithParams
{
    protected function process($one, $two): void
    {
        parent::process($one, $two);
    }
}
