<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\TooLongVariableRule\Fixture;

final class LongVariable
{
    public function run($superLongVariableThatGoesBeyongReadingFewWords = 100)
    {
        return $superLongVariableThatGoesBeyongReadingFewWords;
    }
}
