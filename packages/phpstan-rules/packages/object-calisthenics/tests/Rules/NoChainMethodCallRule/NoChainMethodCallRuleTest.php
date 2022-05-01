<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoChainMethodCallRule;

/**
 * @extends RuleTestCase<NoChainMethodCallRule>
 */
final class NoChainMethodCallRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ChainMethodCall.php', [[NoChainMethodCallRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/SkipSymfonyConfig.php', []];
        yield [__DIR__ . '/Fixture/SkipExtraAllowedClass.php', []];
        yield [__DIR__ . '/Fixture/SkipNullsafeCalls.php', []];
        yield [__DIR__ . '/Fixture/SkipTrinaryLogic.php', []];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/standalone_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoChainMethodCallRule::class);
    }
}
