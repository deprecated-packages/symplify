<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallOnNewRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenMethodCallOnNewRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenMethodCallOnNewRule>
 */
final class ForbiddenMethodCallOnNewRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/MethodCallOnNew.php', [[ForbiddenMethodCallOnNewRule::ERROR_MESSAGE, 7]]];
        yield [__DIR__ . '/Fixture/SkipMethodCallOnVariable.php', []];
        yield [__DIR__ . '/Fixture/SkipNewFinder.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenMethodCallOnNewRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
