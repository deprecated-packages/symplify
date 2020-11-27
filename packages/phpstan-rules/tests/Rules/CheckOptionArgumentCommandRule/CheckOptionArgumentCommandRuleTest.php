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
        yield [__DIR__ . '/Fixture/NotCommand.php', []];
        yield [__DIR__ . '/Fixture/CorrectCommand.php', []];
        yield [
            __DIR__ . '/Fixture/InCorrectCommand1.php',
            [
                [sprintf(CheckOptionArgumentCommandRule::ERROR_MESSAGE, 'addOption', 'getOption'), 16],
            ],
        ];
        yield [
            __DIR__ . '/Fixture/InCorrectCommand2.php',
            [
                [sprintf(CheckOptionArgumentCommandRule::ERROR_MESSAGE, 'addArgument', 'getArgument'), 17],
            ],
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
