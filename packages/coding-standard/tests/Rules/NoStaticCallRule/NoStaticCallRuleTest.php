<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\NoStaticCallRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class NoStaticCallRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeStaticCall.php', [[NoStaticCallRule::ERROR_MESSAGE, 13]]];

        yield [__DIR__ . '/Fixture/SkipAllowedStaticCall.php', []];
        yield [__DIR__ . '/Fixture/SkipAllowedDateTime.php', []];
        yield [__DIR__ . '/Fixture/SkipParentSelfStatic.php', []];
        yield [__DIR__ . '/Fixture/SkipStaticFactory.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoStaticCallRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
