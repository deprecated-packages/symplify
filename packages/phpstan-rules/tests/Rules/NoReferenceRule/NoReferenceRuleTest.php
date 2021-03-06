<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReferenceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoReferenceRule;

final class NoReferenceRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/MethodWithReference.php', [[NoReferenceRule::ERROR_MESSAGE, 9]]];
        yield [__DIR__ . '/Fixture/FunctionWithReference.php', [[NoReferenceRule::ERROR_MESSAGE, 7]]];
        yield [__DIR__ . '/Fixture/VariableReference.php', [[NoReferenceRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/ReferenceArgument.php', [[NoReferenceRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/AssignReference.php', [[NoReferenceRule::ERROR_MESSAGE, 14]]];

        yield [__DIR__ . '/Fixture/SkipUseInReference.php', []];
        yield [__DIR__ . '/Fixture/SkipParentMethodWithReference.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoReferenceRule::class, __DIR__ . '/../../../config/symplify-rules.neon');
    }
}
