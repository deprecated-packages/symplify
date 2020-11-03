<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ClassNameRespectsParentSuffixRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ClassNameRespectsParentSuffixRule;

final class ClassNameRespectsParentSuffixRuleTest extends AbstractServiceAwareRuleTestCase
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

        yield [__DIR__ . '/Fixture/SkipCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipSomeEventSubscriber.php', []];
        yield [__DIR__ . '/Fixture/SkipFixer.php', []];

        $errorMessage = sprintf(ClassNameRespectsParentSuffixRule::ERROR_MESSAGE, 'NonTestSuffix', 'TestCase');
        yield [__DIR__ . '/Fixture/NonTestSuffix.php', [[$errorMessage, 9]]];
        yield [__DIR__ . '/Fixture/SkipTest.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractTestCase.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ClassNameRespectsParentSuffixRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
