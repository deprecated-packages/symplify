<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenProtectedPropertyRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenProtectedPropertyRule;

/**
 * @extends RuleTestCase<ForbiddenProtectedPropertyRule>
 */
final class ForbiddenProtectedPropertyRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAbstractClassWithAutowireAttributeInjection.php', []];
        yield [__DIR__ . '/Fixture/SkipParentGuardedProperty.php', []];
        yield [__DIR__ . '/Fixture/SkipHasNonProtectedPropertyAndConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractClassWithConstructorInjection.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractClassWithConstructorSetValues.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractClassWithAutowireInjection.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractClassWithTestCaseSetUp.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractAnyTestCase.php', []];

        yield [__DIR__ . '/Fixture/HasProtectedPropertyAndConstant.php', [
            [ForbiddenProtectedPropertyRule::ERROR_MESSAGE, 11],
            [ForbiddenProtectedPropertyRule::ERROR_MESSAGE, 15],
        ]];
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
        return self::getContainer()->getByType(ForbiddenProtectedPropertyRule::class);
    }
}
