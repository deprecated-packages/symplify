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
final class RequireInvokableControllerRulePhp80Test extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array<string|int[]|string[]>>
     */
    public function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/MissnamedRouteAttributeController.php',
            [[RequireInvokableControllerRule::ERROR_MESSAGE, 12]],
        ];
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
