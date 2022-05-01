<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMultipleClassLikeInOneFileRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenMultipleClassLikeInOneFileRule;

/**
 * @extends RuleTestCase<ForbiddenMultipleClassLikeInOneFileRule>
 */
final class ForbiddenMultipleClassLikeInOneFileRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipOneInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipOneClassWithAnonymousClass.php', []];
        yield [__DIR__ . '/Fixture/SkipOneClass.php', []];
        yield [__DIR__ . '/Fixture/SkipOneTrait.php', []];

        yield [
            __DIR__ . '/Fixture/MultipleClassLike.php',
            [[ForbiddenMultipleClassLikeInOneFileRule::ERROR_MESSAGE, 3]],
        ];
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
        return self::getContainer()->getByType(ForbiddenMultipleClassLikeInOneFileRule::class);
    }
}
