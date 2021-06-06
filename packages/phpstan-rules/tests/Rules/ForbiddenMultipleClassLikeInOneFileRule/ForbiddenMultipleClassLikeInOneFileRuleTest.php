<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMultipleClassLikeInOneFileRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenMultipleClassLikeInOneFileRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenMultipleClassLikeInOneFileRule>
 */
final class ForbiddenMultipleClassLikeInOneFileRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipOneInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipOneClassWithAnonymousClass.php', []];
        yield [__DIR__ . '/Fixture/SkipOneClass.php', []];
        yield [__DIR__ . '/Fixture/SkipOneTrait.php', []];

        yield [
            __DIR__ . '/Fixture/MultipleClassLike.php',
            [[ForbiddenMultipleClassLikeInOneFileRule::ERROR_MESSAGE, 3]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenMultipleClassLikeInOneFileRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
