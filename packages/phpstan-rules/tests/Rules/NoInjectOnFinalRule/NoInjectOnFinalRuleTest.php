<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoInjectOnFinalRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoInjectOnFinalRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoInjectOnFinalRule>
 */
final class NoInjectOnFinalRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipInjectOnNonAbstract.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractClass.php', []];

        yield [
            __DIR__ . '/Fixture/InjectOnNonAbstractWithAbstractParent.php',
            [[NoInjectOnFinalRule::ERROR_MESSAGE, 15]],
        ];

        yield [
            __DIR__ . '/Fixture/RequireOnNonAbstractWithAbstractParent.php',
            [[NoInjectOnFinalRule::ERROR_MESSAGE, 15]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoInjectOnFinalRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
