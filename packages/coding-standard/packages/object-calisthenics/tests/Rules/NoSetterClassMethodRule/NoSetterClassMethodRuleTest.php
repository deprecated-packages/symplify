<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\NoSetterClassMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\NoSetterClassMethodRule;

final class NoSetterClassMethodRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(NoSetterClassMethodRule::ERROR_MESSAGE, 'setName');
        yield [__DIR__ . '/Fixture/SetterMethod.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/AllowedClass.php', []];
    }

    protected function getRule(): Rule
    {
        return new NoSetterClassMethodRule(['*\AllowedClass']);
    }
}
