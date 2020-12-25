<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenNullableParameterRule;

final class ForbiddenNullableParameterRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(ForbiddenNullableParameterRule::ERROR_MESSAGE, 'value');
        yield [__DIR__ . '/Fixture/MethodWithNullableParam.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/SkipAllowedType.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenNullableParameterRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
