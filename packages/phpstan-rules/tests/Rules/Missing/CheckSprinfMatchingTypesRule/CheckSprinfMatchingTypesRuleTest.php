<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprinfMatchingTypesRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Missing\CheckSprinfMatchingTypesRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<CheckSprinfMatchingTypesRule>
 */
final class CheckSprinfMatchingTypesRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/MissMatchSprinft.php', [[CheckSprinfMatchingTypesRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipCorrectSprinft.php', []];
        yield [__DIR__ . '/Fixture/SkipCorrectForeachKey.php', []];
        yield [__DIR__ . '/Fixture/SkipToString.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckSprinfMatchingTypesRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
