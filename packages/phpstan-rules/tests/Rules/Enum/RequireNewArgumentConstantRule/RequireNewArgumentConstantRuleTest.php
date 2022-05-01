<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireNewArgumentConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Enum\RequireNewArgumentConstantRule;

/**
 * @extends RuleTestCase<RequireNewArgumentConstantRule>
 */
final class RequireNewArgumentConstantRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkippedInstance.php', []];
        yield [__DIR__ . '/Fixture/SkipInputOptionInstanceWithConstantParameter.php', []];

        yield [__DIR__ . '/Fixture/InputOptionInstanceWithNonConstantParameter.php', [
            [sprintf(RequireNewArgumentConstantRule::ERROR_MESSAGE, 2), 9],
        ]];
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
        return self::getContainer()->getByType(RequireNewArgumentConstantRule::class);
    }
}
