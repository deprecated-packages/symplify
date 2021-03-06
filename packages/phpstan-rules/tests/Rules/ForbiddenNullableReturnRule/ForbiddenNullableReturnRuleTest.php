<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableReturnRule;

use Iterator;
use PhpParser\Node;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenNullableReturnRule;

final class ForbiddenNullableReturnRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(ForbiddenNullableReturnRule::ERROR_MESSAGE, Node::class);
        yield [__DIR__ . '/Fixture/MethodWithNullableReturn.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/SkipParentMethodWithNullableReturn.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenNullableReturnRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
