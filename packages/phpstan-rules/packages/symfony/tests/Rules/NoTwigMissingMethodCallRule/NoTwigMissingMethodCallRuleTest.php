<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Symfony\Rules\NoTwigMissingMethodCallRule;
use Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Source\SomeType;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoTwigMissingMethodCallRule>
 */
final class NoTwigMissingMethodCallRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeMissingVariableController.php', [
            [
                sprintf(NoTwigMissingMethodCallRule::ERROR_MESSAGE, 'some_type', SomeType::class, 'nonExistingMethod'),
                17,
            ],
        ]];

        $errorMessages = [
            [
                sprintf(NoTwigMissingMethodCallRule::ERROR_MESSAGE, 'some_type', SomeType::class, 'nonExistingMethod'),
                20,
            ],
            [sprintf(NoTwigMissingMethodCallRule::ERROR_MESSAGE, 'some_type', SomeType::class, 'blabla'), 20],
        ];
        yield [__DIR__ . '/Fixture/SomeForeachMissingVariableController.php', $errorMessages];

        yield [__DIR__ . '/Fixture/SkipExistingMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipExistingProperty.php', []];
        yield [__DIR__ . '/Fixture/SkipExistingArrayAccessItems.php', []];
        yield [__DIR__ . '/Fixture/SkipApp.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoTwigMissingMethodCallRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
