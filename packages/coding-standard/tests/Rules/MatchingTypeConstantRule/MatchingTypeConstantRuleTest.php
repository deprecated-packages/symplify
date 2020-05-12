<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\MatchingTypeConstantRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\MatchingTypeConstantRule;

final class MatchingTypeConstantRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $firstErrorMessage = sprintf(MatchingTypeConstantRule::ERROR_MESSAGE, 'bool', 'string');

        $secondErrorMessage = sprintf(MatchingTypeConstantRule::ERROR_MESSAGE, 'string', 'bool');

        $this->analyse(
            [__DIR__ . '/Source/ClassWithConstants.php'],
            [[$firstErrorMessage, 12], [$secondErrorMessage, 17]]
        );
    }

    protected function getRule(): Rule
    {
        return new MatchingTypeConstantRule();
    }
}
