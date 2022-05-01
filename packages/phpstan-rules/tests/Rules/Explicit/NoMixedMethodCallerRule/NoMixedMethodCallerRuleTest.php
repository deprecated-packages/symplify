<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedMethodCallerRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoMixedMethodCallerRule;

/**
 * @extends RuleTestCase<NoMixedMethodCallerRule>
 */
final class NoMixedMethodCallerRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipKnownCallerType.php', []];

        $errorMessage = sprintf(NoMixedMethodCallerRule::ERROR_MESSAGE, '$someType');
        yield [__DIR__ . '/Fixture/MagicMethodName.php', [[$errorMessage, 11]]];

        $errorMessage = sprintf(NoMixedMethodCallerRule::ERROR_MESSAGE, '$mixedType');
        yield [__DIR__ . '/Fixture/UnknownCallerType.php', [[$errorMessage, 11]]];
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
        return self::getContainer()->getByType(NoMixedMethodCallerRule::class);
    }
}
