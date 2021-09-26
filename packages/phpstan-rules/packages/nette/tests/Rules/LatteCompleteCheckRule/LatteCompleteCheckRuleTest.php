<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Nette\Rules\LatteCompleteCheckRule;
use Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRule\Fixture\InvalidControlRenderArguments;
use Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRule\Source\SomeTypeWithMethods;

/**
 * @extends AbstractServiceAwareRuleTestCase<LatteCompleteCheckRule>
 */
final class LatteCompleteCheckRuleTest extends AbstractServiceAwareRuleTestCase
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
        // tests @see \PHPStan\Rules\Methods\CallMethodsRule
        $errorMessage = sprintf('Call to an undefined method %s::missingMethod().', SomeTypeWithMethods::class);
        yield [__DIR__ . '/Fixture/SomeMissingMethodCall.php', [[$errorMessage, 1]]];

        // tests @see \PHPStan\Rules\Methods\CallMethodsRule
        $errorMessage = sprintf(
            'Parameter #1 $name of method %s::render() expects string, int given.',
            InvalidControlRenderArguments::class
        );
        yield [__DIR__ . '/Fixture/InvalidControlRenderArguments.php', [[$errorMessage, 25]]];

        yield [__DIR__ . '/Fixture/SkipExistingMethodCall.php', []];
        yield [__DIR__ . '/Fixture/SkipVariableInBlockControl.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(LatteCompleteCheckRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
