<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoAbstractRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\NoAbstractRule;

/**
 * @extends RuleTestCase<NoAbstractRule>
 */
final class NoAbstractRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAbstractCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipNonAbstractClass.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractTestCase.php', []];

        yield [__DIR__ . '/Fixture/AbstractClass.php', [[NoAbstractRule::ERROR_MESSAGE, 7]]];
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
        return self::getContainer()->getByType(NoAbstractRule::class);
    }
}
