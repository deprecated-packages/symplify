<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenInlineClassMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\ForbiddenInlineClassMethodRule;

/**
 * @extends RuleTestCase<ForbiddenInlineClassMethodRule>
 */
final class ForbiddenInlineClassMethodRuleTest extends RuleTestCase
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
     * @return Iterator<string[]|array<int, array<int[]|string[]>>>
     */
    public function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/SomeClassWithInlinedMethod.php', [
                [sprintf(ForbiddenInlineClassMethodRule::ERROR_MESSAGE, 'away'), 14],
            ], ];

        yield [__DIR__ . '/Fixture/SkipUsedTwice.php', []];
        yield [__DIR__ . '/Fixture/SkipNoMethodCall.php', []];
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
        return self::getContainer()->getByType(ForbiddenInlineClassMethodRule::class);
    }
}
