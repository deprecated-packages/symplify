<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMagicClosureRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\NoMagicClosureRule;

/**
 * @extends RuleTestCase<NoMagicClosureRule>
 */
final class NoMagicClosureRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/MagicClosure.php', [[NoMagicClosureRule::ERROR_MESSAGE, 7]]];
        yield [__DIR__ . '/Fixture/SkipClosureAssign.php', []];
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
        return self::getContainer()->getByType(NoMagicClosureRule::class);
    }
}
