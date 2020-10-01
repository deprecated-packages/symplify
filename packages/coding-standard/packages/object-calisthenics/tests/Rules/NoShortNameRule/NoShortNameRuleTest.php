<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\NoShortNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\NoShortNameRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class NoShortNameRuleTest extends AbstractServiceAwareRuleTestCase
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
        $emErrorMessage = sprintf(NoShortNameRule::ERROR_MESSAGE, 'em', 3);
        $yeErrorMEssage = sprintf(NoShortNameRule::ERROR_MESSAGE, 'YE', 3);

        yield [__DIR__ . '/Fixture/ShortNamingClass.php', [[$emErrorMessage, 9], [$yeErrorMEssage, 11]]];

        yield [__DIR__ . '/Fixture/SkipId.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoShortNameRule(3, ['id']);
    }
}
