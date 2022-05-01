<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\UppercaseConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\UppercaseConstantRule;

/**
 * @extends RuleTestCase<UppercaseConstantRule>
 */
final class UppercaseConstantRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<int, array<int, array<int, int|string>|string>>
     */
    public function provideData(): Iterator
    {
        $errorMessage = sprintf(UppercaseConstantRule::ERROR_MESSAGE, 'SMall');
        yield [__DIR__ . '/Fixture/ConstantLower.php', [[$errorMessage, 9]]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(UppercaseConstantRule::class);
    }
}
