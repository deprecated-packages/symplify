<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\TooManyMethodsRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooManyMethodsRule;

final class TooManyMethodsRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array<string|int[]|string[]>>
     */
    public function provideData(): Iterator
    {
        $message = sprintf(TooManyMethodsRule::ERROR_MESSAGE, 4, 3);
        yield [__DIR__ . '/Fixture/ManyMethods.php', [[$message, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(TooManyMethodsRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
