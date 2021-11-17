<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanLatteRules\Rules\LatteCompleteCheckRule;
use Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Fixture\InvalidControlRenderArguments;
use Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Source\ExampleModel;
use Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Source\SomeTypeWithMethods;

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
//        yield [__DIR__ . '/Fixture/SomeMissingMethodCall.php', [[$errorMessage, 12]]];

        // tests @see \PHPStan\Rules\Methods\CallMethodsRule
        $errorMessage = sprintf(
            'Parameter #1 $name of method %s::render() expects string, int given.',
            InvalidControlRenderArguments::class
        );
        yield [__DIR__ . '/Fixture/InvalidControlRenderArguments.php', [[$errorMessage, 15]]];

        yield [__DIR__ . '/Fixture/SkipExistingMethodCall.php', []];
        yield [__DIR__ . '/Fixture/SkipVariableInBlockControl.php', []];

        yield [__DIR__ . '/Fixture/GetTemplateAndReplaceExtension.php', $this->createSharedErrorMessages(15)];
        yield [__DIR__ . '/Fixture/NoAdditionalPropertyRead.php', $this->createSharedErrorMessages(15)];
        yield [__DIR__ . '/Fixture/PropertyReadTemplate.php', $this->createSharedErrorMessages(19)];
        yield [__DIR__ . '/Fixture/RenderWithParameters.php', $this->createSharedErrorMessages(15)];
//        yield [
//            __DIR__ . '/Fixture/TemplateAsVariableAndRenderToStringWithParameters.php',
//            $this->createSharedErrorMessages(16),
//        ];

        yield [__DIR__ . '/Fixture/OneActionPresenter.php', $this->createSharedErrorMessages(15)];

        $multiActionsPresenterErrors = array_merge(
            $this->createSharedErrorMessages(15),
            $this->createSharedErrorMessages(21),
        );
        yield [__DIR__ . '/Fixture/MultiActionsAndRendersPresenter.php', $multiActionsPresenterErrors];

        $errorMessages = [
            ['Variable $nonExistingVariable might not be defined.', 16],
            ['Call to an undefined method Nette\Security\User::nonExistingMethod().', 16],
            [sprintf('Call to an undefined method %s::getTitle().', ExampleModel::class), 16],
        ];
        yield [__DIR__ . '/Fixture/ControlWithForm.php', $errorMessages];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(LatteCompleteCheckRule::class, __DIR__ . '/config/configured_rule.neon');
    }

    /**
     * @return array<array<string|int>>
     */
    private function createSharedErrorMessages(int $phpLine): array
    {
        return [
            ['Variable $nonExistingVariable might not be defined.', $phpLine],
            ['Call to an undefined method Nette\Security\User::nonExistingMethod().', $phpLine],
            [sprintf('Call to an undefined method %s::getTitle().', ExampleModel::class), $phpLine],
            [
                'Method ' . InvalidControlRenderArguments::class . '::render() invoked with 2 parameters, 1 required.',
                $phpLine,
            ],
            [
                'Parameter #1 $name of method ' . InvalidControlRenderArguments::class . '::render() expects string, int given.',
                $phpLine,
            ],
        ];
    }
}
