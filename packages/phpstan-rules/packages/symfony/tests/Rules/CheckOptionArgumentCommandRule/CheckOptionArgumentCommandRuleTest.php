<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\CheckOptionArgumentCommandRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Symfony\Rules\CheckOptionArgumentCommandRule;

/**
 * @extends RuleTestCase<CheckOptionArgumentCommandRule>
 */
final class CheckOptionArgumentCommandRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipCorrectCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipCorrectSelfConstantCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractCommand.php', []];

        yield [
            __DIR__ . '/Fixture/NonExecuteClassMethodCommand.php',
            [[sprintf(CheckOptionArgumentCommandRule::ERROR_MESSAGE, 'categorize'), 12]],
        ];

        yield [
            __DIR__ . '/Fixture/IncorrectCommand1.php',
            [[sprintf(CheckOptionArgumentCommandRule::ERROR_MESSAGE, 'categorize'), 12]],
        ];
        yield [
            __DIR__ . '/Fixture/IncorrectCommand2.php',
            [[sprintf(CheckOptionArgumentCommandRule::ERROR_MESSAGE, 'sources", "enabled'), 11]],
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
        return self::getContainer()->getByType(CheckOptionArgumentCommandRule::class);
    }
}
