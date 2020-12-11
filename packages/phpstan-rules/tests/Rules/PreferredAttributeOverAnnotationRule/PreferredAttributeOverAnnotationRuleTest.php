<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredAttributeOverAnnotationRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\PreferredAttributeOverAnnotationRule;

final class PreferredAttributeOverAnnotationRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @requires PHP 8.0
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

        $errorMessage = sprintf(
            PreferredAttributeOverAnnotationRule::ERROR_MESSAGE,
            'Symfony\Component\Routing\Annotation\Route'
        );

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
