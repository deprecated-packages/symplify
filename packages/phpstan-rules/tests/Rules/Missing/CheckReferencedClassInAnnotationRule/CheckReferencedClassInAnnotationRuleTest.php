<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckReferencedClassInAnnotationRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Missing\CheckReferencedClassInAnnotationRule;
use Symplify\PHPStanRules\Tests\Rules\Missing\CheckReferencedClassInAnnotationRule\Source\ExistingClass;

/**
 * @extends AbstractServiceAwareRuleTestCase<CheckReferencedClassInAnnotationRule>
 */
final class CheckReferencedClassInAnnotationRuleTest extends AbstractServiceAwareRuleTestCase
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
            CheckReferencedClassInAnnotationRule::ERROR_MESSAGE,
            'Symplify\PHPStanRules\Tests\Rules\Missing\CheckReferencedClassInAnnotationRule\Fixture\Blemc'
        );
        yield [__DIR__ . '/Fixture/NonExistingClassAnnotation.php', [[$errorMessage, 12]]];
        yield [__DIR__ . '/Fixture/NonExistingClassAnnotationInConstantFetch.php', [[$errorMessage, 12]]];

        yield [__DIR__ . '/Fixture/SkipSeeAnnotation.php', []];
        yield [__DIR__ . '/Fixture/SkipExistingClassAnnotation.php', []];
        yield [__DIR__ . '/Fixture/SkipExistingClassAnnotationWithConstant.php', []];

        // check constants
        $errorMessage = sprintf(
            CheckReferencedClassInAnnotationRule::CONSTANT_ERROR_MESSAGE,
            'NOT_HERE',
            ExistingClass::class
        );
        yield [__DIR__ . '/Fixture/ExistingClassAnnotationButMissingConstant.php', [[$errorMessage, 13]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckReferencedClassInAnnotationRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
