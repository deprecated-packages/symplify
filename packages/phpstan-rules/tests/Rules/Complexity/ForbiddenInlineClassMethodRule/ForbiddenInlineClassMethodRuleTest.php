<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenInlineClassMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\ForbiddenInlineClassMethodRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenInlineClassMethodRule>
 */
final class ForbiddenInlineClassMethodRuleTest extends AbstractServiceAwareRuleTestCase
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

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenInlineClassMethodRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
