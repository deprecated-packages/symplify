<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckConstantStringValueFormatRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\CheckConstantStringValueFormatRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class CheckConstantStringValueFormatRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/NotHasClassConstant.php', []];
        yield [__DIR__ . '/Fixture/NotHasStringClassConstant.php', []];
        yield [__DIR__ . '/Fixture/HasValidStringClassConstant.php', []];
        yield [__DIR__ . '/Fixture/Invalid.php', [[CheckConstantStringValueFormatRule::ERROR_MESSAGE, 9]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckConstantStringValueFormatRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
