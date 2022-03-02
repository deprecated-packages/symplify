<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenNamedArgumentsRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\ForbiddenNamedArgumentsRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenNamedArgumentsRule>
 */
final class ForbiddenNamedArgumentsRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAttributeNamedArguments.php', []];
        yield [__DIR__ . '/Fixture/SkipNormalArguments.php', []];

        yield [
            __DIR__ . '/Fixture/ClassWithNamedArguments.php',
            [[ForbiddenNamedArgumentsRule::ERROR_MESSAGE, 11]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenNamedArgumentsRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
