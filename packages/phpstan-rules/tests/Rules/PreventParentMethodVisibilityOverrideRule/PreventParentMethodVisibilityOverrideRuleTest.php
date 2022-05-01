<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventParentMethodVisibilityOverrideRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\PreventParentMethodVisibilityOverrideRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<PreventParentMethodVisibilityOverrideRule>
 */
final class PreventParentMethodVisibilityOverrideRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(PreventParentMethodVisibilityOverrideRule::ERROR_MESSAGE, 'run', 'protected');
        yield [__DIR__ . '/Fixture/ClassWithOverridingVisibility.php', [[$errorMessage, 9]]];
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
        return self::getContainer()->getByType(PreventParentMethodVisibilityOverrideRule::class);
    }
}
