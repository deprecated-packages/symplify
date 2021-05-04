<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoIssetOnObjectRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoIssetOnObjectRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoIssetOnObjectRule>
 */
final class NoIssetOnObjectRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/IssetOnObject.php', [[NoIssetOnObjectRule::ERROR_MESSAGE, 17]]];

        yield [__DIR__ . '/Fixture/SkipIssetOnArray.php', []];
        yield [__DIR__ . '/Fixture/SkipIssetOnArrayNestedOnObject.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoIssetOnObjectRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
