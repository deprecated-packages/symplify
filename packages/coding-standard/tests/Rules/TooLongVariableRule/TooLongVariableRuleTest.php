<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\TooLongVariableRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\TooLongVariableRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class TooLongVariableRuleTest extends AbstractServiceAwareRuleTestCase
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
        return $this->getRuleFromConfig(TooLongVariableRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
