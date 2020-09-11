<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\MatchingTypeConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\MatchingTypeConstantRule;

final class MatchingTypeConstantRuleTest extends RuleTestCase
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
        $firstErrorMessage = sprintf(MatchingTypeConstantRule::ERROR_MESSAGE, 'bool', 'string');
        $secondErrorMessage = sprintf(MatchingTypeConstantRule::ERROR_MESSAGE, 'string', 'bool');

        yield [__DIR__ . '/Source/ClassWithConstants.php', [[$firstErrorMessage, 12], [$secondErrorMessage, 17]]];
    }

    protected function getRule(): Rule
    {
        return new MatchingTypeConstantRule();
    }
}
