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
        yield [__DIR__ . '/Fixture/InvalidControlRenderArguments.php', [[$errorMessage, 1]]];

        yield [__DIR__ . '/Fixture/SkipExistingMethodCall.php', []];
        yield [__DIR__ . '/Fixture/SkipVariableInBlockControl.php', []];


        $errorMessages = [
//            [
//                'Static method Latte\Runtime\Filters::date() invoked with 3 parameters, 1-2 required.',
//                2
//            ],
//            [
//                'Parameter #2 $format of static method Latte\Runtime\Filters::date() expects string|null, int given.',
//                2
//            ],
            [
                'Variable $nonExistingVariable might not be defined.',
                3
            ],
            [
                'Call to an undefined method Nette\Security\User::nonExistingMethod().',
                6
            ],
            [
                'Call to an undefined method Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRule\Source\ExampleModel::getTitle().',
                9
            ],
            [
                'Method ' . InvalidControlRenderArguments::class . '::render() invoked with 2 parameters, 1 required.',
                12
            ],
            [
                'Parameter #1 $name of method ' . InvalidControlRenderArguments::class . '::render() expects string, int given.',
                12
            ],
        ];

        yield [__DIR__ . '/Fixture/GetTemplateAndReplaceExtension.php', $errorMessages];
        yield [__DIR__ . '/Fixture/NoAdditionalPropertyRead.php', $errorMessages];
        yield [__DIR__ . '/Fixture/PropertyReadTemplate.php', $errorMessages];
        yield [__DIR__ . '/Fixture/RenderWithParameters.php', $errorMessages];
        yield [__DIR__ . '/Fixture/TemplateAsVariableAndRenderToStringWithParameters.php', $errorMessages];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(LatteCompleteCheckRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
