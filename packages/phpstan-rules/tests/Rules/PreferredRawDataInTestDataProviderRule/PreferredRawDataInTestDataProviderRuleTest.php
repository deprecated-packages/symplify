<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredRawDataInTestDataProviderRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\PreferredRawDataInTestDataProviderRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<PreferredRawDataInTestDataProviderRule>
 */
final class PreferredRawDataInTestDataProviderRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipUseRawDataForTestDataProviderTest.php', []];

        yield [
            __DIR__ . '/Fixture/UseDataFromSetupInTestDataProviderTest.php',
            [[PreferredRawDataInTestDataProviderRule::ERROR_MESSAGE, 24]],
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
