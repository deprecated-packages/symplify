<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNewOutsideFactoryServiceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenNewOutsideFactoryServiceRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenNewOutsideFactoryServiceRule>
 */
final class ForbiddenNewOutsideFactoryServiceRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipStarFactory.php', []];
        yield [__DIR__ . '/Fixture/SkipNonStarFactory.php', []];

        yield [__DIR__ . '/Fixture/NotFactoryClass.php', [
            [sprintf(ForbiddenNewOutsideFactoryServiceRule::ERROR_MESSAGE, 'Foo'), 11],
        ]];
        yield [__DIR__ . '/Fixture/NotAFactoryClassStar.php', [
            [sprintf(ForbiddenNewOutsideFactoryServiceRule::ERROR_MESSAGE, '*Search'), 11],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenNewOutsideFactoryServiceRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
