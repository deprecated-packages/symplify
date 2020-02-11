<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\Rules\ClassMethod;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanExtensions\Rules\ClassMethod\BoolishClassMethodPrefixRule;

final class BoolishClassMethodPrefixRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $analysedFilePath, array $expectedErrorsWithLine): void
    {
        $this->analyse([$analysedFilePath], $expectedErrorsWithLine);
    }

    public function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Source/ClassWithBoolishMethods.php',
            [
                ['Method "honesty()" returns bool type, so the name should start with is/has/was...', 9],
                ['Method "thatWasGreat()" returns bool type, so the name should start with is/has/was...', 14],
            ],
        ];

        yield [__DIR__ . '/Source/ClassWithEmptyReturn.php', []];

        yield [__DIR__ . '/Source/ClassThatImplementsInterface.php', []];

        yield [__DIR__ . '/Source/SkipRequiredByInterface.php', []];
    }

    protected function getRule(): Rule
    {
        return new BoolishClassMethodPrefixRule();
    }
}
