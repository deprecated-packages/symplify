<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenComplexFuncCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenComplexFuncCallRule;

final class ForbiddenComplexFuncCallRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(ForbiddenComplexFuncCallRule::ERROR_MESSAGE, 'array_filter');
        yield [__DIR__ . '/Fixture/ComlextArrayFilter.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipSimpleArrayFilter.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenComplexFuncCallRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
