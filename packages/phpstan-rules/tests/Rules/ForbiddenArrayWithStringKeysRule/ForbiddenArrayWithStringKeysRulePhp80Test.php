<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\Contract\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\ForbiddenArrayWithStringKeysRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenArrayWithStringKeysRule>
 */
final class ForbiddenArrayWithStringKeysRulePhp80Test extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
{
    /**
     * @param mixed[] $expectedErrorMessagesWithLines
     * @dataProvider provideData()
     */
    public function test(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array<int, mixed[]|string>>
     */
    public function provideData(): Iterator
    {
        yield [__DIR__ . '/FixturePhp80/SkipAttributeArrayKey.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenArrayWithStringKeysRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
