<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\MatchingTypeConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\MatchingTypeConstantRule;

final class MatchingTypeConstantRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $firstErrorMessage = sprintf(MatchingTypeConstantRule::ERROR_MESSAGE, 'bool', 'string');
        $secondErrorMessage = sprintf(MatchingTypeConstantRule::ERROR_MESSAGE, 'string', 'bool');

        yield [__DIR__ . '/Fixture/ClassWithConstants.php', [[$firstErrorMessage, 12], [$secondErrorMessage, 17]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            MatchingTypeConstantRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
