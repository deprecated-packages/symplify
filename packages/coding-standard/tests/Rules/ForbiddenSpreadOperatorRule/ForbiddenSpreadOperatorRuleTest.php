<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenSpreadOperatorRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenSpreadOperatorRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenSpreadOperatorRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/NoSpreadOperator.php', []];
        yield [__DIR__ . '/Fixture/SpreadOperator.php', [[ForbiddenSpreadOperatorRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenSpreadOperatorRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
