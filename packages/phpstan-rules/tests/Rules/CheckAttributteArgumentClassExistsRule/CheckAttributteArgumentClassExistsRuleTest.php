<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckAttributteArgumentClassExistsRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckAttributteArgumentClassExistsRule;

/**
 * @requires PHP 8.0
 *
 * @extends AbstractServiceAwareRuleTestCase<CheckAttributteArgumentClassExistsRule>
 */
final class CheckAttributteArgumentClassExistsRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipExistingClassAttributeArgument.php', []];

        yield [__DIR__ . '/Fixture/SomeClassWithAttributeArgumentMissingClass.php', [
            [CheckAttributteArgumentClassExistsRule::ERROR_MESSAGE, 9],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckAttributteArgumentClassExistsRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
