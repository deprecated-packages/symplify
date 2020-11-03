<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoParentMethodCallOnNoOverrideProcessRule;

final class NoParentMethodCallOnNoOverrideProcessRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipParentWithArgs.php', []];
        yield [__DIR__ . '/Fixture/SkipNotCallParentMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipParentMethodCallOverride.php', []];
        yield [__DIR__ . '/Fixture/SkipParentMethodCallInsideExpression.php', []];
        yield [__DIR__ . '/Fixture/SkipParentMethodCallFromDifferentMethodName.php', []];

        yield [
            __DIR__ . '/Fixture/ParentMethodCallNoOverride.php',
            [[NoParentMethodCallOnNoOverrideProcessRule::ERROR_MESSAGE, 11]],
        ];

        yield [
            __DIR__ . '/Fixture/OverrideWithSameParamsAndArgs.php',
            [[NoParentMethodCallOnNoOverrideProcessRule::ERROR_MESSAGE, 11]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoParentMethodCallOnNoOverrideProcessRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
