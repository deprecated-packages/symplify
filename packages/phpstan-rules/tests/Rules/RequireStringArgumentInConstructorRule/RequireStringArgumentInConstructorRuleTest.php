<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInConstructorRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireStringArgumentInConstructorRule;
use Symplify\PHPStanRules\Tests\Rules\RequireStringArgumentInMethodCallRule\Source\AlwaysCallMeWithString;

final class RequireStringArgumentInConstructorRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<mixed>
     */
    public function provideData(): Iterator
    {
        $errorMessage = sprintf(
            RequireStringArgumentInConstructorRule::ERROR_MESSAGE,
            AlwaysCallMeWithString::class,
            1
        );
        yield [__DIR__ . '/Fixture/WithClassConstant.php', [[$errorMessage, 14]]];

        yield [__DIR__ . '/Fixture/SkipWithConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipWithString.php', []];
        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireStringArgumentInConstructorRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
