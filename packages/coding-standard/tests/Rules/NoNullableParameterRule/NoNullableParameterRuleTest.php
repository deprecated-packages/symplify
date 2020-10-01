<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoNullableParameterRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\NoNullableParameterRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class NoNullableParameterRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(NoNullableParameterRule::ERROR_MESSAGE, 'value');
        yield [__DIR__ . '/Fixture/MethodWithNullableParam.php', [[$errorMessage, 9]]];
    }

    protected function getRule(): Rule
    {
        return new NoNullableParameterRule();
    }
}
