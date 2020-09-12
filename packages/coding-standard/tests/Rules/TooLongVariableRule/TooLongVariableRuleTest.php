<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\TooLongVariableRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\TooLongVariableRule;

final class TooLongVariableRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $message = sprintf(
            TooLongVariableRule::ERROR_MESSAGE,
            'superLongVariableThatGoesBeyongReadingFewWords',
            46,
            10
        );
        yield [__DIR__ . '/Fixture/LongVariable.php', [[$message, 11]]];
    }

    protected function getRule(): Rule
    {
        return new TooLongVariableRule(10);
    }
}
