<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\RequireAttributeNamespaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Domain\RequireAttributeNamespaceRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireAttributeNamespaceRule>
 */
final class RequireAttributeNamespaceRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/MisslocatedAttribute.php', [[RequireAttributeNamespaceRule::ERROR_MESSAGE, 7]]];
        yield [__DIR__ . '/Fixture/Attribute/SkipCorrectAttribute.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireAttributeNamespaceRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
