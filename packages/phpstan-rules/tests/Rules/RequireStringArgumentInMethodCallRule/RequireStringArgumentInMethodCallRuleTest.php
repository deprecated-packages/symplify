<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireStringArgumentInMethodCallRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireStringArgumentInMethodCallRule>
 */
final class RequireStringArgumentInMethodCallRuleTest extends AbstractServiceAwareRuleTestCase
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
     * @return Iterator<mixed>
     */
    public function provideData(): Iterator
    {
        $errorMessage = sprintf(RequireStringArgumentInMethodCallRule::ERROR_MESSAGE, 'callMe', 1);
        yield [__DIR__ . '/Fixture/WithClassConstant.php', [[$errorMessage, 15]]];

        yield [__DIR__ . '/Fixture/SkipWithConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipWithString.php', []];
        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireStringArgumentInMethodCallRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
