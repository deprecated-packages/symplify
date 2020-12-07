<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\OnlyOneClassMethodRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\OnlyOneClassMethodRule\Source\DoubleParentInterface;

final class SkipOneUsed implements DoubleParentInterface
{
    public function go()
    {
    }
}
