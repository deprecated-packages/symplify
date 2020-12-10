<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireInvokableControllerRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireInvokableControllerRule;

final class Php80Test extends AbstractServiceAwareRuleTestCase
{
    /**
     * @requires PHP 8.0
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

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
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
