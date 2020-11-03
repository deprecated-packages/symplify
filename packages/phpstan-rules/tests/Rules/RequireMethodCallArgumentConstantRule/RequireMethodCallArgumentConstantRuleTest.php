<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireMethodCallArgumentConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireMethodCallArgumentConstantRule;

final class RequireMethodCallArgumentConstantRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(RequireMethodCallArgumentConstantRule::ERROR_MESSAGE, 0);
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstant.php', [[$errorMessage, 14]]];
        yield [__DIR__ . '/Fixture/SymfonyPHPConfigParameterSetter.php', [[$errorMessage, 14]]];

        yield [__DIR__ . '/Fixture/NestedNode.php', [[$errorMessage, 14], [$errorMessage, 19]]];
        yield [__DIR__ . '/Fixture/IntersetionNode.php', [[$errorMessage, 17]]];

        yield [__DIR__ . '/Fixture/WithConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireMethodCallArgumentConstantRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
