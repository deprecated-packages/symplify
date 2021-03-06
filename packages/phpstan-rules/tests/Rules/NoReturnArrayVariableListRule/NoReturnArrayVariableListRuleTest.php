<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnArrayVariableListRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoReturnArrayVariableListRule;

final class NoReturnArrayVariableListRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/ReturnVariables.php', [[NoReturnArrayVariableListRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipReturnOne.php', []];
        yield [__DIR__ . '/Fixture/SkipNews.php', []];
        yield [__DIR__ . '/Fixture/ValueObject/SkipValueObject.php', []];
        yield [__DIR__ . '/Fixture/SkipParentMethod.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoReturnArrayVariableListRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
