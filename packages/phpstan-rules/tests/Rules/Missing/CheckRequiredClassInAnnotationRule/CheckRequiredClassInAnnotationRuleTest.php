<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckRequiredClassInAnnotationRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Missing\CheckRequiredClassInAnnotationRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<CheckRequiredClassInAnnotationRule>
 */
final class CheckRequiredClassInAnnotationRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(
            CheckRequiredClassInAnnotationRule::ERROR_MESSAGE,
            'Symplify\PHPStanRules\Tests\Rules\Missing\CheckRequiredClassInAnnotationRule\Fixture\Blemc'
        );
        yield [__DIR__ . '/Fixture/NonExistingClassAnnotation.php', [[$errorMessage, 12]]];

        yield [__DIR__ . '/Fixture/SkipExistingClassAnnotation.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckRequiredClassInAnnotationRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
