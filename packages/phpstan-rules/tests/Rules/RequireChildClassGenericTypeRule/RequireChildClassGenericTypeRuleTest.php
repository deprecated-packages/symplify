<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireChildClassGenericTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireChildClassGenericTypeRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireChildClassGenericTypeRule>
 */
final class RequireChildClassGenericTypeRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|string[]|int[]> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipAbstract.php', []];
        yield [__DIR__ . '/Fixture/SkipCorrect.php', []];

        yield [__DIR__ . '/Fixture/MissingGenericType.php', [[RequireChildClassGenericTypeRule::ERROR_MESSAGE, 9]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireChildClassGenericTypeRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
