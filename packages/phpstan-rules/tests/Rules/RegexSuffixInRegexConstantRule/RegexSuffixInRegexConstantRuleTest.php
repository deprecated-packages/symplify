<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RegexSuffixInRegexConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RegexSuffixInRegexConstantRule;

final class RegexSuffixInRegexConstantRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
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
        $errorMessage = sprintf(RegexSuffixInRegexConstantRule::ERROR_MESSAGE, 'SOME_NAME');

        yield [__DIR__ . '/Fixture/DifferentSuffix.php', [[$errorMessage, 15]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RegexSuffixInRegexConstantRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
