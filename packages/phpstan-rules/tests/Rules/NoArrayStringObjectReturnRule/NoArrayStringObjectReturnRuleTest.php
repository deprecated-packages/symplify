<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayStringObjectReturnRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\NoArrayStringObjectReturnRule;

/**
 * @extends RuleTestCase<NoArrayStringObjectReturnRule>
 */
final class NoArrayStringObjectReturnRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
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

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoArrayStringObjectReturnRule::class);
    }
}
