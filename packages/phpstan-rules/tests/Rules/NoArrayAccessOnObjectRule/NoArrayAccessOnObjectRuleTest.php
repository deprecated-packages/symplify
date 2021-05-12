<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayAccessOnObjectRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoArrayAccessOnObjectRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoArrayAccessOnObjectRule>
 */
final class NoArrayAccessOnObjectRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/ArrayAccessOnObject.php', [[NoArrayAccessOnObjectRule::ERROR_MESSAGE, 14]]];
        yield [__DIR__ . '/Fixture/ArrayAccessOnNestedObject.php', [[NoArrayAccessOnObjectRule::ERROR_MESSAGE, 14]]];

        yield [__DIR__ . '/Fixture/SkipOnArray.php', []];
        yield [__DIR__ . '/Fixture/SkipSplFixedArray.php', []];
        yield [__DIR__ . '/Fixture/SkipXml.php', []];
        yield [__DIR__ . '/Fixture/SkipXmlElementForeach.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoArrayAccessOnObjectRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
