<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoMirrorAssertRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\NoMirrorAssertRule;

/**
 * @extends RuleTestCase<NoMirrorAssertRule>
 */
final class NoMirrorAssertRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipDifferentAssert.php', []];
        yield [__DIR__ . '/Fixture/SkipNoTestCase.php', []];
        yield [__DIR__ . '/Fixture/SkipNonAssertMirror.php', []];

        yield [__DIR__ . '/Fixture/AssertMirror.php', [[NoMirrorAssertRule::ERROR_MESSAGE, 13]]];
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
        return self::getContainer()->getByType(NoMirrorAssertRule::class);
    }
}
