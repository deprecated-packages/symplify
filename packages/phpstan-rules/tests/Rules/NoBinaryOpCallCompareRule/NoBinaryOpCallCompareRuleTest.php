<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoBinaryOpCallCompareRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoBinaryOpCallCompareRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoBinaryOpCallCompareRule>
 */
final class NoBinaryOpCallCompareRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/CompareToFuncCall.php', [[NoBinaryOpCallCompareRule::ERROR_MESSAGE, 8]]];

        yield [__DIR__ . '/Fixture/SkipAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipConcat.php', []];
        yield [__DIR__ . '/Fixture/SkipFuncCallCount.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoBinaryOpCallCompareRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
