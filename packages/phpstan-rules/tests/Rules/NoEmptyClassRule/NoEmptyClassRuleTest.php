<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoEmptyClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\NoEmptyClassRule;

/**
 * @extends RuleTestCase<NoEmptyClassRule>
 */
final class NoEmptyClassRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAttribute.php', []];
        yield [__DIR__ . '/Fixture/SkipMarkerInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipException.php', []];
        yield [__DIR__ . '/Fixture/SkipWithCommentInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipWithContent.php', []];
        yield [__DIR__ . '/Fixture/SkipWithCommentAbove.php', []];
        yield [__DIR__ . '/Fixture/SkipFinalChildOfAbstract.php', []];
        yield [__DIR__ . '/Fixture/SkipEmptyClassWithImplements.php', []];

        yield [__DIR__ . '/Fixture/SomeEmptyClass.php', [[NoEmptyClassRule::ERROR_MESSAGE, 7]]];
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
        return self::getContainer()->getByType(NoEmptyClassRule::class);
    }
}
