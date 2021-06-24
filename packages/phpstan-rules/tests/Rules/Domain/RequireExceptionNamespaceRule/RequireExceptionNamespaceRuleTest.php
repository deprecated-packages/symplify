<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\RequireExceptionNamespaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Domain\RequireExceptionNamespaceRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireExceptionNamespaceRule>
 */
final class RequireExceptionNamespaceRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/MisslocatedException.php', [[RequireExceptionNamespaceRule::ERROR_MESSAGE, 7]]];
        yield [__DIR__ . '/Fixture/Exception/SkipCorrectException.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireExceptionNamespaceRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
