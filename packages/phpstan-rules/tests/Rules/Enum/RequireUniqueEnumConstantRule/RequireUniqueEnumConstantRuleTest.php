<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireUniqueEnumConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Enum\RequireUniqueEnumConstantRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireUniqueEnumConstantRule>
 */
final class RequireUniqueEnumConstantRuleTest extends AbstractServiceAwareRuleTestCase
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
        $expectedErrorMessage = sprintf(RequireUniqueEnumConstantRule::ERROR_MESSAGE, 'yes');
        yield [__DIR__ . '/Fixture/InvalidEnum.php', [[$expectedErrorMessage, 8]]];

        yield [__DIR__ . '/Fixture/SkipValidEnum.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireUniqueEnumConstantRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
