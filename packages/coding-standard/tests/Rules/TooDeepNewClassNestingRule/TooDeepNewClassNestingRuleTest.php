<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\TooDeepNewClassNestingRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\TooDeepNewClassNestingRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class TooDeepNewClassNestingRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/InsideFunction.php', []];
        yield [__DIR__ . '/Fixture/AllowedDeepNewClass.php', []];
        yield [__DIR__ . '/Fixture/TooDeepNewClassInSameLevel.php', [
            [sprintf(TooDeepNewClassNestingRule::ERROR_MESSAGE, 3, 4), 7],
        ]];
        yield [__DIR__ . '/Fixture/TooDeepNewClass.php', [
            [sprintf(TooDeepNewClassNestingRule::ERROR_MESSAGE, 3, 5), 7],
            [sprintf(TooDeepNewClassNestingRule::ERROR_MESSAGE, 3, 4), 8],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(TooDeepNewClassNestingRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
