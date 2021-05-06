<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoConstantInterfaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoConstantInterfaceRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoConstantInterfaceRule>
 */
final class NoConstantInterfaceRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipInterfaceWithMethods.php', []];
        yield [__DIR__ . '/Fixture/InterfaceWithConstants.php', [[NoConstantInterfaceRule::ERROR_MESSAGE, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoConstantInterfaceRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
