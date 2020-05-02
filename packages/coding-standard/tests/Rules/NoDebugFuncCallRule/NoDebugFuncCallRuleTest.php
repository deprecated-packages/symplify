<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoDebugFuncCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoDebugFuncCallRule;

final class NoDebugFuncCallRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], [$expectedErrorMessagesWithLines]);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(NoDebugFuncCallRule::ERROR_MESSAGE, 'dump');
        yield [__DIR__ . '/Fixture/DebugFuncCall.php', [$errorMessage, 11]];
    }

    protected function getRule(): Rule
    {
        return new NoDebugFuncCallRule();
    }
}
