<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoStaticCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoStaticCallRule;

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
        yield [__DIR__ . '/Fixture/SkipStaticMask.php', []];
        yield [__DIR__ . '/Fixture/SkipSymfonyPhpConfig.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoStaticCallRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
