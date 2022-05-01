<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\SuffixTraitRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\SuffixTraitRule;

/**
 * @extends RuleTestCase<SuffixTraitRule>
 */
final class SuffixTraitRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCorrectlyNameTrait.php', []];

        yield [__DIR__ . '/Fixture/TraitWithoutSuffix.php', [[SuffixTraitRule::ERROR_MESSAGE, 7]]];
        yield [__DIR__ . '/Fixture/SomeNotTrait.php', [[SuffixTraitRule::ERROR_MESSAGE, 7]]];
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
        return self::getContainer()->getByType(SuffixTraitRule::class);
    }
}
