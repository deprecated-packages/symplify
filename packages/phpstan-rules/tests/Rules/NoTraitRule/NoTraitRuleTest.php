<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoTraitRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoTraitRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoTraitRule>
 */
final class NoTraitRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeTrait.php', [[NoTraitRule::ERROR_MESSAGE, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoTraitRule::class, __DIR__ . '/../../../config/symplify-rules.neon');
    }
}
