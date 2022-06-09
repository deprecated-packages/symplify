<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenArrayWithStringKeysRule;

/**
 * @extends RuleTestCase<ForbiddenArrayWithStringKeysRule>
 */
final class ForbiddenArrayWithStringKeysRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ArrayWithStrings.php', [[ForbiddenArrayWithStringKeysRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipJsonNamed.php', []];
        yield [__DIR__ . '/Fixture/SkipJsonSerializable.php', []];
        yield [__DIR__ . '/Fixture/SkipArrayRequiredParentContract.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInTest.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInTestCase.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInGetDefinition.php', []];
        yield [__DIR__ . '/Fixture/SkipNonConstantString.php', []];
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
        return self::getContainer()->getByType(ForbiddenArrayWithStringKeysRule::class);
    }
}
