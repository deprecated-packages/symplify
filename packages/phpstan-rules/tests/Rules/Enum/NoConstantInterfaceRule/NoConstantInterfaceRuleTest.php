<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\NoConstantInterfaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Enum\NoConstantInterfaceRule;

/**
 * @extends RuleTestCase<NoConstantInterfaceRule>
 */
final class NoConstantInterfaceRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipInterfaceWithMethods.php', []];
        yield [__DIR__ . '/Fixture/InterfaceWithConstants.php', [[NoConstantInterfaceRule::ERROR_MESSAGE, 7]]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoConstantInterfaceRule::class);
    }
}
