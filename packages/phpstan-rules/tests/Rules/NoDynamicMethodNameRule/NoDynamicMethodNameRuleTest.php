<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicMethodNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoDynamicMethodNameRule;

final class NoDynamicMethodNameRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/DynamicMethodCallName.php', [[NoDynamicMethodNameRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/DynamicStaticMethodCallName.php', [[NoDynamicMethodNameRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/DynamicFuncCallName.php', [[NoDynamicMethodNameRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipClosure.php', []];
        yield [__DIR__ . '/Fixture/SkipCallable.php', []];
        yield [__DIR__ . '/Fixture/SkipNullableClosure.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoDynamicMethodNameRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
