<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenNamedArgumentsRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\ForbiddenNamedArgumentsRule;

/**
 * @extends RuleTestCase<ForbiddenNamedArgumentsRule>
 */
final class ForbiddenNamedArgumentsRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAttributeNamedArguments.php', []];
        yield [__DIR__ . '/Fixture/SkipNormalArguments.php', []];

        yield [
            __DIR__ . '/Fixture/ClassWithNamedArguments.php',
            [[ForbiddenNamedArgumentsRule::ERROR_MESSAGE, 11]],
        ];
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
        return self::getContainer()->getByType(ForbiddenNamedArgumentsRule::class);
    }
}
