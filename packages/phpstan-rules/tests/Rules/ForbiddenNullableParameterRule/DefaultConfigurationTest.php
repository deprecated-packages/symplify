<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenNullableParameterRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenNullableParameterRule>
 */
final class DefaultConfigurationTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(ForbiddenNullableParameterRule::ERROR_MESSAGE, 'name');
        yield [__DIR__ . '/Fixture/MethodWithNullableScalar.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/SkipParentContract.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenNullableParameterRule::class,
            __DIR__ . '/config/default_configuration.neon'
        );
    }
}
