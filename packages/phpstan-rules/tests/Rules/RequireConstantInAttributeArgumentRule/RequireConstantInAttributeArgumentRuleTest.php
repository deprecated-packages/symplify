<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireConstantInAttributeArgumentRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\RequireConstantInAttributeArgumentRule;

/**
 * @extends RuleTestCase<RequireConstantInAttributeArgumentRule>
 */
final class RequireConstantInAttributeArgumentRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCheckedAttribute.php', []];
        yield [__DIR__ . '/Fixture/SkipSomeAttributeWithConstant.php', []];

        $errorMessage = sprintf(RequireConstantInAttributeArgumentRule::ERROR_MESSAGE, 'name');
        yield [__DIR__ . '/Fixture/AttributeWithString.php', [[$errorMessage, 11]]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(RequireConstantInAttributeArgumentRule::class);
    }
}
