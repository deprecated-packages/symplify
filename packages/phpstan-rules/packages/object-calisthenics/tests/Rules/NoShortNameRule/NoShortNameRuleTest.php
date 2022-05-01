<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoShortNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoShortNameRule;

/**
 * @extends RuleTestCase<NoShortNameRule>
 */
final class NoShortNameRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipId.php', []];

        $errorMessage = sprintf(NoShortNameRule::ERROR_MESSAGE, 'em', 3);
        $yeErrorMEssage = sprintf(NoShortNameRule::ERROR_MESSAGE, 'YE', 3);
        yield [__DIR__ . '/Fixture/ShortNamingClass.php', [[$errorMessage, 9], [$yeErrorMEssage, 11]]];

        $errorMessage = sprintf(NoShortNameRule::ERROR_MESSAGE, 'n', 3);
        yield [__DIR__ . '/Fixture/ShortClosureParam.php', [[$errorMessage, 11]]];
        yield [__DIR__ . '/Fixture/ShortParam.php', [[$errorMessage, 9]]];

        $errorMessage = sprintf(NoShortNameRule::ERROR_MESSAGE, 'n', 3);
        yield [
            __DIR__ . '/Fixture/ShortAssignParameter.php',
            [[$errorMessage, 11], [$errorMessage, 13], [$errorMessage, 15]], ];
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
        return self::getContainer()->getByType(NoShortNameRule::class);
    }
}
