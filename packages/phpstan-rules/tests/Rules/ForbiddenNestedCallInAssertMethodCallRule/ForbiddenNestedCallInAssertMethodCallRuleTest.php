<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNestedCallInAssertMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenNestedCallInAssertMethodCallRule;

final class ForbiddenNestedCallInAssertMethodCallRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipAssertNothing.php', []];
        yield [__DIR__ . '/Fixture/SkipCleanAssert.php', []];
        yield [__DIR__ . '/Fixture/SkipSimpleGetter.php', []];
        yield [__DIR__ . '/Fixture/SkipAssertTrue.php', []];

        yield [__DIR__ . '/Fixture/NestedAssertMethodCall.php', [
            [ForbiddenNestedCallInAssertMethodCallRule::ERROR_MESSAGE, 14],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenNestedCallInAssertMethodCallRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
