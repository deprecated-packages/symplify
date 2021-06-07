<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnSetterMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoReturnSetterMethodRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoReturnSetterMethodRule>
 */
final class NoReturnSetterMethodRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeSetterClass.php', [[NoReturnSetterMethodRule::ERROR_MESSAGE, 9]]];

        yield [__DIR__ . '/Fixture/SkipEmptyReturn.php', []];
        yield [__DIR__ . '/Fixture/SkipVoidSetter.php', []];
        yield [__DIR__ . '/Fixture/SkipSetUp.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoReturnSetterMethodRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
