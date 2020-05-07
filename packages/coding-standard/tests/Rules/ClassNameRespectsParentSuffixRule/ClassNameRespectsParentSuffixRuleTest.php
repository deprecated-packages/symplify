<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ClassNameRespectsParentSuffixRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\ClassNameRespectsParentSuffixRule;

final class ClassNameRespectsParentSuffixRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(ClassNameRespectsParentSuffixRule::ERROR_MESSAGE, 'SomeController', 'Command');
        yield [__DIR__ . '/Fixture/SomeController.php', [[$errorMessage, 9]]];

        $errorMessage = sprintf(
            ClassNameRespectsParentSuffixRule::ERROR_MESSAGE,
            'SomeEventSubscriberFalse',
            'EventSubscriber'
        );
        yield [__DIR__ . '/Fixture/SomeEventSubscriberFalse.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/DebugFunctionCallSniff.php', []];

        yield [__DIR__ . '/Fixture/SkipCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipSomeEventSubscriber.php', []];
        yield [__DIR__ . '/Fixture/SkipFixer.php', []];
    }

    protected function getRule(): Rule
    {
        return new ClassNameRespectsParentSuffixRule();
    }
}
