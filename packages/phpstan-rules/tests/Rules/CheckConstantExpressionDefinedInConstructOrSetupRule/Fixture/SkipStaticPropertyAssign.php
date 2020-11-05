<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class SkipStaticPropertyAssign
{
    /**
     * @var bool
     */
    private static $isEnabled = false;

    public function enable()
    {
        self::$isEnabled = true;
    }
}
