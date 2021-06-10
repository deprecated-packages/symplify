<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMaskWithoutSprintfRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoMaskWithoutSprintfRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoMaskWithoutSprintfRule>
 */
final class NoMaskWithoutSprintfRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipWithSprintf.php', []];
        yield [__DIR__ . '/Fixture/SkipOnConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipHerenowdoc.php', []];

        yield [__DIR__ . '/Fixture/NoSprintf.php', [[NoMaskWithoutSprintfRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoMaskWithoutSprintfRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
