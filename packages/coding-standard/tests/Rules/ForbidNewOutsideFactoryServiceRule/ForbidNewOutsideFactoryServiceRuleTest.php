<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbidNewOutsideFactoryServiceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbidNewOutsideFactoryServiceRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbidNewOutsideFactoryServiceRuleTest extends AbstractServiceAwareRuleTestCase
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
            [sprintf(ForbidNewOutsideFactoryServiceRule::ERROR_MESSAGE, 'Foo'), 11],
        ]];
        yield [__DIR__ . '/Fixture/NotAFactoryClassStar.php', [
            [sprintf(ForbidNewOutsideFactoryServiceRule::ERROR_MESSAGE, '*Search'), 11],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbidNewOutsideFactoryServiceRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
