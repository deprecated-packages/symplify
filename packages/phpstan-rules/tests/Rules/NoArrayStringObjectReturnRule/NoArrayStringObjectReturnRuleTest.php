<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayStringObjectReturnRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoArrayStringObjectReturnRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoArrayStringObjectReturnRule>
 */
final class NoArrayStringObjectReturnRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/ArrayStringObjectReturn.php', [
            [NoArrayStringObjectReturnRule::ERROR_MESSAGE, 18],
            [NoArrayStringObjectReturnRule::ERROR_MESSAGE, 26],
        ]];

        yield [
            __DIR__ . '/Fixture/WithoutPropertyArrayStringObjectReturn.php',
            [[NoArrayStringObjectReturnRule::ERROR_MESSAGE, 13]],
        ];

        yield [__DIR__ . '/Fixture/ParamArrayStringObject.php', [[NoArrayStringObjectReturnRule::ERROR_MESSAGE, 16]]];

        yield [__DIR__ . '/Fixture/SkipNonStringKey.php', []];
        yield [__DIR__ . '/Fixture/SkipArrayFilter.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoArrayStringObjectReturnRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
