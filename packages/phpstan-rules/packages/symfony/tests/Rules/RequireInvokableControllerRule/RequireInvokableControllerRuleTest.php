<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\RequireInvokableControllerRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Symfony\Rules\RequireInvokableControllerRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireInvokableControllerRule>
 */
final class RequireInvokableControllerRuleTest extends AbstractServiceAwareRuleTestCase
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
