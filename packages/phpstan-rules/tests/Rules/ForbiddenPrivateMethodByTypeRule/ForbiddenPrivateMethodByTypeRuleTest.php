<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenPrivateMethodByTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenPrivateMethodByTypeRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenPrivateMethodByTypeRule>
 */
final class ForbiddenPrivateMethodByTypeRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAbstractCommand.php', []];

        yield [__DIR__ . '/Fixture/ConsoleCommand.php', [[ForbiddenPrivateMethodByTypeRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenPrivateMethodByTypeRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
