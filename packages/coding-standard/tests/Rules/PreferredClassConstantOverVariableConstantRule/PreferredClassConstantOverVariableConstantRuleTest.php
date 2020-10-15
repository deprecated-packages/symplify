<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredClassConstantOverVariableConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\PreferredClassConstantOverVariableConstantRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class PreferredClassConstantOverVariableConstantRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipClassContantFetch.php', []];

        yield [
            __DIR__ . '/Fixture/VariableConstant.php',
            [[PreferredClassConstantOverVariableConstantRule::ERROR_MESSAGE, 14]],
        ];

        yield [
            __DIR__ . '/Fixture/ArrayDimFetchConstant.php',
            [[PreferredClassConstantOverVariableConstantRule::ERROR_MESSAGE, 17]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            PreferredClassConstantOverVariableConstantRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
