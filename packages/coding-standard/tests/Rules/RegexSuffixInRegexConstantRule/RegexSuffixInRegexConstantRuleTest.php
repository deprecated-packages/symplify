<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RegexSuffixInRegexConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\RegexSuffixInRegexConstantRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class RegexSuffixInRegexConstantRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(RegexSuffixInRegexConstantRule::ERROR_MESSAGE, 'SOME_NAME');

        yield [__DIR__ . '/Fixture/DifferentSuffix.php', [[$errorMessage, 15]]];
    }

    protected function getRule(): Rule
    {
        return new RegexSuffixInRegexConstantRule();
    }
}
