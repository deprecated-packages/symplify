<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenParamTypeRemovalRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenParamTypeRemovalRule>
 */
final class ForbiddenParamTypeRemovalRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipPhpDocType.php', []];
        yield [__DIR__ . '/Fixture/SkipPresentType.php', []];
        yield [__DIR__ . '/Fixture/SkipNoType.php', []];

        yield [__DIR__ . '/Fixture/SkipIndirectRemoval.php', []];

        yield [__DIR__ . '/Fixture/RemoveParentType.php', [[ForbiddenParamTypeRemovalRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenParamTypeRemovalRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
