<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoAbstractRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\NoAbstractRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoAbstractRule>
 */
final class NoAbstractRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNonAbstractClass.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractTestCase.php', []];
        yield [__DIR__ . '/Fixture/AbstractClass.php', [[NoAbstractRule::ERROR_MESSAGE, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoAbstractRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
