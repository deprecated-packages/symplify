<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\ForbiddenClassConstRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Enum\ForbiddenClassConstRule;

/**
 * @extends RuleTestCase<ForbiddenClassConstRule>
 */
final class ForbiddenClassConstRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/NotAllowedConstant.php', [[ForbiddenClassConstRule::ERROR_MESSAGE, 9]]];

        yield [__DIR__ . '/Fixture/SkipDifferenteParent.php', []];
        yield [__DIR__ . '/Fixture/SkipValidClassConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipMinMaxConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipMinMaxConstantSuffix.php', []];
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
        return self::getContainer()->getByType(ForbiddenClassConstRule::class);
    }
}
