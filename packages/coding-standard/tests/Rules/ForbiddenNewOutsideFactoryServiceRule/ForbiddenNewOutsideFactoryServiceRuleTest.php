<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenNewOutsideFactoryServiceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenNewOutsideFactoryServiceRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenNewOutsideFactoryServiceRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/StarFactory.php', []];
        yield [__DIR__ . '/Fixture/NonStarFactory.php', []];
        yield [__DIR__ . '/Fixture/NotAFactoryClassNonStar.php', [
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
