<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\TooLongVariableRule\Fixture;

final class LongVariable
{
    public function run($superLongVariableThatGoesBeyongReadingFewWords = 100)
    {
        return $superLongVariableThatGoesBeyongReadingFewWords;
    }
}
