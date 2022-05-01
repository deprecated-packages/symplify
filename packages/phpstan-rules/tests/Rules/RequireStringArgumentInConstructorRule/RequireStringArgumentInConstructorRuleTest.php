<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInConstructorRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\RequireStringArgumentInConstructorRule;
use Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInConstructorRule\Source\AlwaysCallMeWithString;

/**
 * @extends RuleTestCase<RequireStringArgumentInConstructorRule>
 */
final class RequireStringArgumentInConstructorRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<mixed>
     */
    public function provideData(): Iterator
    {
        $errorMessage = sprintf(
            RequireStringArgumentInConstructorRule::ERROR_MESSAGE,
            AlwaysCallMeWithString::class,
            1
        );
        yield [__DIR__ . '/Fixture/WithClassConstant.php', [[$errorMessage, 14]]];

        yield [__DIR__ . '/Fixture/SkipWithConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipWithString.php', []];
        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];
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
        return self::getContainer()->getByType(RequireStringArgumentInConstructorRule::class);
    }
}
