<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenNewOutsideFactoryRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenNewOutsideFactoryRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenNewOutsideFactoryRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/AFactory.php', []];
        yield [__DIR__ . '/Fixture/NotAFactoryClass.php', [
            [sprintf(ForbiddenNewOutsideFactoryRule::ERROR_MESSAGE, '*Search'), 9],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenNewOutsideFactoryRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
