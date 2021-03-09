<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFuncCallInMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoFuncCallInMethodCallRule;

final class NoFuncCallInMethodCallRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(NoFuncCallInMethodCallRule::ERROR_MESSAGE, 'strlen');
        yield [__DIR__ . '/Fixture/FunctionCallNestedToMethodCall.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipGetCwd.php', []];
        yield [__DIR__ . '/Fixture/SkipNamespacedFunction.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoFuncCallInMethodCallRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
