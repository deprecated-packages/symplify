<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireMethodCallArgumentConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireMethodCallArgumentConstantRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireMethodCallArgumentConstantRule>
 */
final class RequireMethodCallArgumentConstantRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipWithConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];

        $errorMessage = sprintf(RequireMethodCallArgumentConstantRule::ERROR_MESSAGE, 0);
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstant.php', [[$errorMessage, 14]]];
        yield [__DIR__ . '/Fixture/SymfonyPHPConfigParameterSetter.php', [[$errorMessage, 14]]];

        yield [__DIR__ . '/Fixture/NestedNode.php', [[$errorMessage, 14], [$errorMessage, 19]]];
        yield [__DIR__ . '/Fixture/IntersectionNode.php', [[$errorMessage, 17]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireMethodCallArgumentConstantRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
