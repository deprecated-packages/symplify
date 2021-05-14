<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\SuffixTraitRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\SuffixTraitRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<SuffixTraitRule>
 */
final class SuffixTraitRuleTest extends AbstractServiceAwareRuleTestCase
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

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(SuffixTraitRule::class, __DIR__ . '/../../../config/symplify-rules.neon');
    }
}
