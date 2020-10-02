<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredRawDataInTestDataProvider;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\PreferredRawDataInTestDataProvider;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class PreferredRawDataInTestDataProviderTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/UseRawDataInTestDataProviderTest.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            PreferredRawDataInTestDataProvider::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
