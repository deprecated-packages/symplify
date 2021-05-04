<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\UppercaseConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\UppercaseConstantRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<UppercaseConstantRule>
 */
final class UppercaseConstantRuleTest extends AbstractServiceAwareRuleTestCase
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
     * @return Iterator<int, array<int, array<int, int|string>|string>>
     */
    public function provideData(): Iterator
    {
        $errorMessage = sprintf(UppercaseConstantRule::ERROR_MESSAGE, 'SMall');
        yield [__DIR__ . '/Fixture/ConstantLower.php', [[$errorMessage, 9]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            UppercaseConstantRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
