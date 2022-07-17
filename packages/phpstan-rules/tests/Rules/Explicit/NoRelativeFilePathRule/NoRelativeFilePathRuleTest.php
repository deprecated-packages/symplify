<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoRelativeFilePathRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoRelativeFilePathRule;

/**
 * @extends RuleTestCase<NoRelativeFilePathRule>
 */
final class NoRelativeFilePathRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAbsoluteFilePath.php', []];
        yield [__DIR__ . '/Fixture/SkipSimpleString.php', []];
        yield [__DIR__ . '/Fixture/SkipNotAFileExtension.php', []];

        yield [__DIR__ . '/Fixture/RelativeFilePath.php', [[NoRelativeFilePathRule::ERROR_MESSAGE, 11]]];
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
        return self::getContainer()->getByType(NoRelativeFilePathRule::class);
    }
}
