<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFunctionCallInMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoFunctionCallInMethodCallRule;

final class NoFunctionCallInMethodCallRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(NoFunctionCallInMethodCallRule::ERROR_MESSAGE, 'strlen');
        yield [__DIR__ . '/Fixture/FunctionCallNestedToMethodCall.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipGetCwd.php', []];
        yield [__DIR__ . '/Fixture/SkipNamespacedFunction.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoFunctionCallInMethodCallRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
