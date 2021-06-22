<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireInvokableControllerRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireInvokableControllerRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireInvokableControllerRule>
 */
final class Php80Test extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<int|string> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array<string|int[]|string[]>>
     */
    public function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/MissnamedRouteAttributeController.php',
            [[RequireInvokableControllerRule::ERROR_MESSAGE, 12]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireInvokableControllerRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
