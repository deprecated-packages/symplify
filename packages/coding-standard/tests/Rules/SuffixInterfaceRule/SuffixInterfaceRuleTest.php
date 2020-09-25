<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\SuffixInterfaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\SuffixInterfaceRule;

final class SuffixInterfaceRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/CorrectlyNameInterface.php', []];

        $errorMessage = sprintf(SuffixInterfaceRule::ERROR_MESSAGE, 'InterfaceWithoutSuffix');
        yield [__DIR__ . '/Fixture/InterfaceWithoutSuffix.php', [[$errorMessage, 7]]];
    }

    protected function getRule(): Rule
    {
        return new SuffixInterfaceRule();
    }
}
