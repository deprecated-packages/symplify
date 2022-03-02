<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireConstantInMethodCallPositionRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Enum\RequireConstantInMethodCallPositionRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireConstantInMethodCallPositionRule>
 */
final class RequireConstantInMethodCallPositionRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipWithConstantLocal.php', []];
        yield [__DIR__ . '/Fixture/SkipWithConstantExternal.php', []];
        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];

        $errorMessageLocal = sprintf(RequireConstantInMethodCallPositionRule::ERROR_MESSAGE, 0);
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstantLocal.php', [[$errorMessageLocal, 14]]];

        $errorMessageExternal = sprintf(RequireConstantInMethodCallPositionRule::ERROR_MESSAGE, 0);
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstantExternal.php', [[$errorMessageExternal, 14]]];

        yield [__DIR__ . '/Fixture/SkipWithConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];

        $errorMessage = sprintf(RequireConstantInMethodCallPositionRule::ERROR_MESSAGE, 0);
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstant.php', [[$errorMessage, 14]]];
        yield [__DIR__ . '/Fixture/SymfonyPHPConfigParameterSetter.php', [[$errorMessage, 14]]];

        yield [__DIR__ . '/Fixture/NestedNode.php', [[$errorMessage, 14], [$errorMessage, 19]]];
        yield [__DIR__ . '/Fixture/IntersectionNode.php', [[$errorMessage, 17]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireConstantInMethodCallPositionRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
