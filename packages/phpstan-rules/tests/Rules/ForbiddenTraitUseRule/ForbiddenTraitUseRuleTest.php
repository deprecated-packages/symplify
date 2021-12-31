<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenTraitUseRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenTraitUseRule;
use Symplify\PHPStanRules\Tests\Rules\ForbiddenTraitUseRule\Source\SomeSmartObjectTrait;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenTraitUseRule>
 */
final class ForbiddenTraitUseRuleTest extends AbstractServiceAwareRuleTestCase
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
     * @return Iterator<array<int, array<int[]|string[]>>|string[]>
     */
    public function provideData(): Iterator
    {
        $errorMessage = sprintf(ForbiddenTraitUseRule::ERROR_MESSAGE, SomeSmartObjectTrait::class);
        yield [__DIR__ . '/Fixture/ClassWithSmartObject.php', [[$errorMessage, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ForbiddenTraitUseRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
