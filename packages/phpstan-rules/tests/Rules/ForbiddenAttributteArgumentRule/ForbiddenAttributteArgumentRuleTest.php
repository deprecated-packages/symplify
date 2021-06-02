<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAttributteArgumentRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenAttributteArgumentRule;

/**
 * @requires PHP 8.0
 *
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenAttributteArgumentRule>
 */
final class ForbiddenAttributteArgumentRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<string[]|array<int, array<int[]|string[]>>>
     */
    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SomeClassWithAttributeArgumentMissingClass.php', [
            [sprintf(ForbiddenAttributteArgumentRule::ERROR_MESSAGE, 'forbiddenKey'), 9],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenAttributteArgumentRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
