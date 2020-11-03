<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventParentMethodVisibilityOverrideRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\PreventParentMethodVisibilityOverrideRule;

final class PreventParentMethodVisibilityOverrideRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(PreventParentMethodVisibilityOverrideRule::ERROR_MESSAGE, 'run', 'protected');
        yield [__DIR__ . '/Fixture/ClassWithOverridingVisibility.php', [[$errorMessage, 9]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            PreventParentMethodVisibilityOverrideRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
