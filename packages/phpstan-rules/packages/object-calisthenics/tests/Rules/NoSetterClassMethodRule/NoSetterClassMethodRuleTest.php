<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoSetterClassMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoSetterClassMethodRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoSetterClassMethodRule>
 */
final class NoSetterClassMethodRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAllowedClass.php', []];

        $errorMessage = sprintf(NoSetterClassMethodRule::ERROR_MESSAGE, 'setName');
        yield [__DIR__ . '/Fixture/SetterMethod.php', [[$errorMessage, 9]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoSetterClassMethodRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
