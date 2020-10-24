<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\CheckParentChildMethodParameterTypeCompatibleRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class CheckParentChildMethodParameterTypeCompatibleRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/NoParent.php', []];
        yield [__DIR__ . '/Fixture/NotHasParentMethod.php', []];
        yield [__DIR__ . '/Fixture/HasSameParameterWithParentMethod.php', []];
        yield [__DIR__ . '/Fixture/HasSameParameterWithInterfaceMethod.php', []];
        yield [
            __DIR__ . '/Fixture/HasDifferentParameterWithParentMethod.php',
            [[CheckParentChildMethodParameterTypeCompatibleRule::ERROR_MESSAGE, 9]],
        ];
        yield [
            __DIR__ . '/Fixture/HasDifferentParameterWithInterfaceMethod.php',
            [[CheckParentChildMethodParameterTypeCompatibleRule::ERROR_MESSAGE, 11], [
                CheckParentChildMethodParameterTypeCompatibleRule::ERROR_MESSAGE,
                15,
            ]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckParentChildMethodParameterTypeCompatibleRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
