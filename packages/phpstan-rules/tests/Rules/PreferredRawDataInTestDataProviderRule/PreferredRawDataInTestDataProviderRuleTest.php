<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredRawDataInTestDataProviderRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\PreferredRawDataInTestDataProviderRule;

/**
 * @extends RuleTestCase<PreferredRawDataInTestDataProviderRule>
 */
final class PreferredRawDataInTestDataProviderRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNoDataProviderTest.php', []];
        yield [__DIR__ . '/Fixture/SkipUseRawDataForTestDataProviderTest.php.inc', []];

        yield [
            __DIR__ . '/Fixture/UseDataFromSetupInTestDataProviderTest.php.inc',
            [[PreferredRawDataInTestDataProviderRule::ERROR_MESSAGE, 18]],
        ];
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
        return self::getContainer()->getByType(PreferredRawDataInTestDataProviderRule::class);
    }
}
