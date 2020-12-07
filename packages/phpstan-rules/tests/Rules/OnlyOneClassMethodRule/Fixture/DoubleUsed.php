<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\OnlyOneClassMethodRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\OnlyOneClassMethodRule\Source\DoubleParentInterface;

final class DoubleUsed implements DoubleParentInterface
{
    public function run()
    {
    }

    public function go()
    {
    }
}
