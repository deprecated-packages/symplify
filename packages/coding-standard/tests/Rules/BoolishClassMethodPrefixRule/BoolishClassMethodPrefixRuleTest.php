<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\BoolishClassMethodPrefixRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\BoolishClassMethodPrefixRule;

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
        $firstErrorMessage = sprintf(BoolishClassMethodPrefixRule::ERROR_MESSAGE, 'honesty');
        $secondErrorMessage = sprintf(BoolishClassMethodPrefixRule::ERROR_MESSAGE, 'thatWasGreat');

        yield [
            __DIR__ . '/Source/ClassWithBoolishMethods.php',
            [[$firstErrorMessage, 9], [$secondErrorMessage, 14]],
        ];

        // no erros
        yield [__DIR__ . '/Source/ClassWithEmptyReturn.php', []];
        yield [__DIR__ . '/Source/ClassThatImplementsInterface.php', []];
        yield [__DIR__ . '/Source/SkipRequiredByInterface.php', []];
    }

    protected function getRule(): Rule
    {
        return new BoolishClassMethodPrefixRule();
    }
}
