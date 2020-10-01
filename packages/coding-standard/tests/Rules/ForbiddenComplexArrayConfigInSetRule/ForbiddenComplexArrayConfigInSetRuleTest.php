<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenComplexArrayConfigInSetRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenComplexArrayConfigInSetRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenComplexArrayConfigInSetRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/ComplexConfig.php', [[ForbiddenComplexArrayConfigInSetRule::ERROR_MESSAGE, 15]]];

        yield [__DIR__ . '/Fixture/SkipSimpleConfig.php', []];
    }

    protected function getRule(): Rule
    {
        return new ForbiddenComplexArrayConfigInSetRule();
    }
}
