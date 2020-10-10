<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireConstantInMethodCallPositionRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\RequireConstantInMethodCallPositionRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class RequireConstantInMethodCallPositionRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(RequireConstantInMethodCallPositionRule::ERROR_MESSAGE, 0, 'local');
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstant.php', [[$errorMessage, 14]]];
        yield [__DIR__ . '/Fixture/WithConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireConstantInMethodCallPositionRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
