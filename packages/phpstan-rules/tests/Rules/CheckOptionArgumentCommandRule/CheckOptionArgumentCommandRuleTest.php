<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckOptionArgumentCommandRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckOptionArgumentCommandRule;

final class CheckOptionArgumentCommandRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
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

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckOptionArgumentCommandRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
