<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireConstantInAttributeArgumentRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireConstantInAttributeArgumentRule;

/**
 * @requires PHP 8.0
 */
final class RequireConstantInAttributeArgumentRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCheckedAttribute.php', []];
        yield [__DIR__ . '/Fixture/SkipSomeAttributeWithConstant.php', []];

        $errorMessage = sprintf(RequireConstantInAttributeArgumentRule::ERROR_MESSAGE, 'name');
        yield [__DIR__ . '/Fixture/AttributeWithString.php', [[$errorMessage, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireConstantInAttributeArgumentRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
