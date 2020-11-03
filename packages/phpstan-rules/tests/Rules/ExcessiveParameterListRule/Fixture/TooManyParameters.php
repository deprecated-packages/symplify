<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ExcessiveParameterListRule\Fixture;

final class TooManyParameters
{
    public function run($one, $two, $three, $four, $five, $six, $seven, $height, $nine, $ten)
    {
    }
}
