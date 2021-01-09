<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireConstantInMethodCallPositionRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\RequireConstantInMethodCallPositionRule\Source\AlwaysCallMeWithConstantLocal;

final class SkipWithConstantLocal
{
    /**
     * @var string
     */
    private const TYPE = 'correct';

    public function run(): void
    {
        $alwaysCallMeWithConstant = new AlwaysCallMeWithConstantLocal();
        $alwaysCallMeWithConstant->call(self::TYPE);
    }
}
