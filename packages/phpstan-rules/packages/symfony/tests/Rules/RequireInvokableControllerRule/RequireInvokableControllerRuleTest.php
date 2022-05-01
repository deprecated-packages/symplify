<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\RequireInvokableControllerRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Symfony\Rules\RequireInvokableControllerRule;

/**
 * @extends RuleTestCase<RequireInvokableControllerRule>
 */
final class RequireInvokableControllerRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipInvokableController.php', []];
        yield [__DIR__ . '/Fixture/SkipRandomPublicMethodController.php', []];

        yield [__DIR__ . '/Fixture/MissnamedController.php', [[RequireInvokableControllerRule::ERROR_MESSAGE, 15]]];
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
        return self::getContainer()->getByType(RequireInvokableControllerRule::class);
    }
}
