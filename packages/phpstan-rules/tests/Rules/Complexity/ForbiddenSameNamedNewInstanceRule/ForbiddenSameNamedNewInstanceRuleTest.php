<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\ForbiddenSameNamedNewInstanceRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenSameNamedNewInstanceRule>
 */
final class ForbiddenSameNamedNewInstanceRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipDifferentNames.php', []];
        yield [__DIR__ . '/Fixture/SkipNonObjectAssigns.php', []];

        $errorMessage = sprintf(ForbiddenSameNamedNewInstanceRule::ERROR_MESSAGE, '$someProduct');
        yield [__DIR__ . '/Fixture/SameObjectAssigns.php', [[$errorMessage, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenSameNamedNewInstanceRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
