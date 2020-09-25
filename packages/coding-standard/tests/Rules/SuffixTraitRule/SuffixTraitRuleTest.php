<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\SuffixTraitRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\SuffixTraitRule;

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
        yield [__DIR__ . '/Fixture/CorrectlyNameTrait.php', []];

        $errorMessage = sprintf(SuffixTraitRule::ERROR_MESSAGE, 'TraitWithoutSuffix');
        yield [__DIR__ . '/Fixture/TraitWithoutSuffix.php', [[$errorMessage, 7]]];
    }

    protected function getRule(): Rule
    {
        return new SuffixTraitRule();
    }
}
