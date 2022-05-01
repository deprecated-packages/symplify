<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprinfMatchingTypesRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Missing\CheckSprinfMatchingTypesRule;

/**
 * @extends RuleTestCase<CheckSprinfMatchingTypesRule>
 */
final class CheckSprinfMatchingTypesRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/MissMatchSprinft.php', [[CheckSprinfMatchingTypesRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipCorrectSprinft.php', []];
        yield [__DIR__ . '/Fixture/SkipCorrectForeachKey.php', []];
        yield [__DIR__ . '/Fixture/SkipToString.php', []];
        yield [__DIR__ . '/Fixture/SkipErrorType.php', []];
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
        return self::getContainer()->getByType(CheckSprinfMatchingTypesRule::class);
    }
}
