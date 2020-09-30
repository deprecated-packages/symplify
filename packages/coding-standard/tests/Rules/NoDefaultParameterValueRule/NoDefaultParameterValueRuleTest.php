<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoDefaultParameterValueRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\NoDefaultParameterValueRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class NoDefaultParameterValueRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(NoDefaultParameterValueRule::ERROR_MESSAGE, 'value');
        yield [__DIR__ . '/Fixture/MethodWithDefaultParamValue.php', [[$errorMessage, 9]]];
    }

    protected function getRule(): Rule
    {
        return new NoDefaultParameterValueRule();
    }
}
