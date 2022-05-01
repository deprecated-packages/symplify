<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMethodTagInClassDocblockRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\NoMethodTagInClassDocblockRule;

/**
 * @extends RuleTestCase<NoMethodTagInClassDocblockRule>
 */
final class NoMethodTagInClassDocblockRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipClassWithNoMethodTag.php', []];
        yield [__DIR__ . '/Fixture/SkipEnum.php', []];

        yield [__DIR__ . '/Fixture/ClassWithMethodTag.php', [[NoMethodTagInClassDocblockRule::ERROR_MESSAGE, 10]]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoMethodTagInClassDocblockRule::class);
    }
}
