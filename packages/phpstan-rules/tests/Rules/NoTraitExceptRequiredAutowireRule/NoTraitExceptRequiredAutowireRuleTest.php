<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoTraitExceptRequiredAutowireRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoTraitExceptRequiredAutowireRule;

final class NoTraitExceptRequiredAutowireRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeTraitWithPublicMethodRequired.php', []];
        yield [__DIR__ . '/Fixture/SomeTrait.php', [[NoTraitExceptRequiredAutowireRule::ERROR_MESSAGE, 7]]];
        yield [
            __DIR__ . '/Fixture/SomeTraitWithoutMethod.php',
            [[NoTraitExceptRequiredAutowireRule::ERROR_MESSAGE, 7]],
        ];
        yield [
            __DIR__ . '/Fixture/SomeTraitWithPublicMethod.php',
            [[NoTraitExceptRequiredAutowireRule::ERROR_MESSAGE, 7]],
        ];
        yield [
            __DIR__ . '/Fixture/SomeTraitWithPrivateMethodRequired.php',
            [[NoTraitExceptRequiredAutowireRule::ERROR_MESSAGE, 7]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoTraitExceptRequiredAutowireRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
