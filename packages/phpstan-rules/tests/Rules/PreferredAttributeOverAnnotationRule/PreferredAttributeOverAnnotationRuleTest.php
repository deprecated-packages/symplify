<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredAttributeOverAnnotationRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\PreferredAttributeOverAnnotationRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<PreferredAttributeOverAnnotationRule>
 */
final class PreferredAttributeOverAnnotationRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAttributeRoute.php', []];

        $errorMessage = sprintf(PreferredAttributeOverAnnotationRule::ERROR_MESSAGE, Route::class);

        yield [__DIR__ . '/Fixture/SomeAnnotatedController.php', [[$errorMessage, 14]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            PreferredAttributeOverAnnotationRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
