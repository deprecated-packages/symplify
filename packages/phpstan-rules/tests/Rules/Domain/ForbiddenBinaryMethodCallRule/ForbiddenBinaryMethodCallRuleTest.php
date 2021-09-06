<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenBinaryMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Domain\ForbiddenBinaryMethodCallRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenBinaryMethodCallRule>
 */
final class ForbiddenBinaryMethodCallRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<int, array<int|string>>|mixed[] $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/BinaryMethodCall.php', [[ForbiddenBinaryMethodCallRule::ERROR_MESSAGE, 13]]];

        yield [__DIR__ . '/Fixture/SkipSearchMethodCall.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenBinaryMethodCallRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
