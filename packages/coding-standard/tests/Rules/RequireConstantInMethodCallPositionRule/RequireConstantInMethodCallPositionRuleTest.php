<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireConstantInMethodCallPositionRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\RequireConstantInMethodCallPositionRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class RequireConstantInMethodCallPositionRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(RequireConstantInMethodCallPositionRule::ERROR_MESSAGE, 0);
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstantLocal.php', [[$errorMessage, 14]]];
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstantExternal.php', [[$errorMessage, 14]]];
        yield [__DIR__ . '/Fixture/SymfonyPHPConfigParameterSetter.php', [[$errorMessage, 14]]];

        yield [__DIR__ . '/Fixture/NestedNode.php', [[$errorMessage, 14], [$errorMessage, 19]]];
        yield [__DIR__ . '/Fixture/IntersetionNode.php', [[$errorMessage, 17]]];

        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];
        yield [__DIR__ . '/Fixture/WithConstantLocal.php', []];
        yield [__DIR__ . '/Fixture/WithConstantExternal.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireConstantInMethodCallPositionRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
