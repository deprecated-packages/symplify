<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\NoDynamicPropertyOnStaticCallRule;

/**
 * @extends RuleTestCase<NoDynamicPropertyOnStaticCallRule>
 */
final class NoDynamicPropertyOnStaticCallRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/DynamicMethodCall.php', [[NoDynamicPropertyOnStaticCallRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/DynamicPropertyCall.php', [[NoDynamicPropertyOnStaticCallRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/SkipNonDynamicPropertyCall.php', []];
        yield [__DIR__ . '/Fixture/SkipNonDynamicMethodCall.php', []];
        yield [__DIR__ . '/Fixture/SkipSelfStatic.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractClassCall.php', []];
        yield [__DIR__ . '/Fixture/SkipUnionTypes.php', []];
        yield [__DIR__ . '/Fixture/SkipObjectClass.php', []];
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
        return self::getContainer()->getByType(NoDynamicPropertyOnStaticCallRule::class);
    }
}
