<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenPrivateMethodByTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symfony\Component\Console\Command\Command;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenPrivateMethodByTypeRule;

final class ForbiddenPrivateMethodByTypeRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/NotConsoleCommand.php', []];
        yield [__DIR__ . '/Fixture/ConsoleCommand.php', [
            [sprintf(ForbiddenPrivateMethodByTypeRule::ERROR_MESSAGE, Command::class), 11],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenPrivateMethodByTypeRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
