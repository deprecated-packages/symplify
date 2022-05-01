<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedArrayDimFetchRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoMixedArrayDimFetchRule;

/**
 * @extends RuleTestCase<NoMixedArrayDimFetchRule>
 */
final class NoMixedArrayDimFetchRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[]|array<int, array<int|string>> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(NoMixedArrayDimFetchRule::ERROR_MESSAGE, '$this->items');
        yield [__DIR__ . '/Fixture/ReportUntypedArray.php', [[$errorMessage, 13]]];

        yield [__DIR__ . '/Fixture/SkipTypedArray.php', []];
        yield [__DIR__ . '/Fixture/SkipString.php', []];
        yield [__DIR__ . '/Fixture/SkipExternalPhpParser.php', []];
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
        return self::getContainer()->getByType(NoMixedArrayDimFetchRule::class);
    }
}
