<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireNewArgumentConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireNewArgumentConstantRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireNewArgumentConstantRule>
 */
final class RequireNewArgumentConstantRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkippedInstance.php', []];
        yield [__DIR__ . '/Fixture/SkipInputOptionInstanceWithConstantParameter.php', []];

        yield [__DIR__ . '/Fixture/InputOptionInstanceWithNonConstantParameter.php', [
            [sprintf(RequireNewArgumentConstantRule::ERROR_MESSAGE, 2), 9],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireNewArgumentConstantRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
