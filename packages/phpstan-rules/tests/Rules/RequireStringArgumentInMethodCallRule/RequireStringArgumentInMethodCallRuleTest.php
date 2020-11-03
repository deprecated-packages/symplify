<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireStringArgumentInMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\RequireStringArgumentInMethodCallRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class RequireStringArgumentInMethodCallRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<mixed>
     */
    public function provideData(): Iterator
    {
        $errorMessage = sprintf(RequireStringArgumentInMethodCallRule::ERROR_MESSAGE, 'callMe', 1);
        yield [__DIR__ . '/Fixture/WithClassConstant.php', [[$errorMessage, 15]]];

        yield [__DIR__ . '/Fixture/WithConstant.php', []];
        yield [__DIR__ . '/Fixture/WithString.php', []];
        yield [__DIR__ . '/Fixture/WithVariable.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireStringArgumentInMethodCallRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
