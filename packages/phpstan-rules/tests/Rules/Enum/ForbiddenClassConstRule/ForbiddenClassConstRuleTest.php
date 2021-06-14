<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\ForbiddenClassConstRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Enum\ForbiddenClassConstRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenClassConstRule>
 */
final class ForbiddenClassConstRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/NotAllowedConstant.php', [[ForbiddenClassConstRule::ERROR_MESSAGE, 9]]];
        yield [__DIR__ . '/Fixture/SkipDifferenteParent.php', []];
        yield [__DIR__ . '/Fixture/SkipValidClassConstant.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ForbiddenClassConstRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
