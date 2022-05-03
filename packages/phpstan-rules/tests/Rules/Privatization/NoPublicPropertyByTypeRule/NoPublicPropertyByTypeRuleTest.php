<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Privatization\NoPublicPropertyByTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Privatization\NoPublicPropertyByTypeRule;

/**
 * @extends RuleTestCase<NoPublicPropertyByTypeRule>
 */
final class NoPublicPropertyByTypeRuleTest extends RuleTestCase
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
     * @return Iterator<array<string|int[]|string[]>>
     */
    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNoPublicProperties.php', []];
        yield [__DIR__ . '/Fixture/SkipNoTypeMatch.php', []];
        yield [__DIR__ . '/Fixture/SomePublicProperties.php', [[NoPublicPropertyByTypeRule::ERROR_MESSAGE, 8]]];
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
        return self::getContainer()->getByType(NoPublicPropertyByTypeRule::class);
    }
}
