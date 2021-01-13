<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DifferentMethodNameToParameterRule\Fixture;

final class SkipDifferentName
{
    public function setApple(string $apple)
    {
    }
}
