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
        yield [__DIR__ . '/Fixture/SkipEmails.php', []];
        yield [__DIR__ . '/Fixture/SkipMaskFinder.php', []];
        yield [__DIR__ . '/Fixture/SkipStrEndsWith.php', []];
        yield [__DIR__ . '/Fixture/SkipRegexConsts.php', []];
        yield [__DIR__ . '/Fixture/SkipHereNowDoc.php', []];
        yield [__DIR__ . '/Fixture/SkipNoFileBefore.php', []];
        yield [__DIR__ . '/Fixture/SkipAbsoluteFilePath.php', []];
        yield [__DIR__ . '/Fixture/SkipSimpleString.php', []];
        yield [__DIR__ . '/Fixture/SkipNotAFileExtension.php', []];
        yield [__DIR__ . '/Fixture/SkipUrls.php', []];

        $errorMessage = sprintf(NoRelativeFilePathRule::ERROR_MESSAGE, 'some_relative_path.txt');
        yield [__DIR__ . '/Fixture/RelativeFilePath.php', [[$errorMessage, 11]]];
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
