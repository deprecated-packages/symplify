<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanLatteRules\Rules\LatteCompleteCheckRule;
use Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Fixture\ControlWithHandle;
use Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Fixture\InvalidControlRenderArguments;
use Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Source\ExampleModel;
use Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Source\FooPresenter;
use Symplify\PHPStanLatteRules\Tests\Rules\LatteCompleteCheckRule\Source\SomeTypeWithMethods;

/**
 * @extends AbstractServiceAwareRuleTestCase<LatteCompleteCheckRule>
 */
final class LatteCompleteCheckRuleTest extends AbstractServiceAwareRuleTestCase
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
        // tests @see \PHPStan\Rules\Methods\CallMethodsRule
        $errorMessage = sprintf('Call to an undefined method %s::missingMethod().', SomeTypeWithMethods::class);
        yield [__DIR__ . '/Fixture/SomeMissingMethodCall.php', [[$errorMessage, 16]]];

        // tests @see \PHPStan\Rules\Methods\CallMethodsRule
        $errorMessage = sprintf(
            'Parameter #1 $name of method %s::render() expects string, int given.',
            InvalidControlRenderArguments::class
        );
        yield [__DIR__ . '/Fixture/InvalidControlRenderArguments.php', [[$errorMessage, 17]]];

        yield [__DIR__ . '/Fixture/SkipExistingMethodCall.php', []];
        yield [__DIR__ . '/Fixture/SkipVariableInBlockControl.php', []];

        yield [__DIR__ . '/Fixture/GetTemplateAndReplaceExtension.php', $this->createSharedErrorMessages(20)];
        yield [__DIR__ . '/Fixture/NoAdditionalPropertyRead.php', $this->createSharedErrorMessages(20)];
        yield [__DIR__ . '/Fixture/PropertyReadTemplate.php', $this->createSharedErrorMessages(24)];
        yield [__DIR__ . '/Fixture/RenderWithParameters.php', $this->createSharedErrorMessages(17)];
        yield [
            __DIR__ . '/Fixture/TemplateAsVariableAndRenderToStringWithParameters.php',
            $this->createSharedErrorMessages(20),
        ];

        yield [__DIR__ . '/Fixture/OneActionPresenter.php', $this->createSharedErrorMessages(10)];

        $multiActionsPresenterErrors = array_merge(
            $this->createSharedErrorMessages(10),
            $this->createSharedErrorMessages(10),
            $this->createSharedErrorMessages(10),
        );
        yield [__DIR__ . '/Fixture/MultiActionsAndRendersPresenter.php', $multiActionsPresenterErrors];

        $errorMessages = [
            ['Variable $nonExistingVariable might not be defined.', 21],
            ['Call to an undefined method Nette\Security\User::nonExistingMethod().', 21],
            [sprintf('Call to an undefined method %s::getTitle().', ExampleModel::class), 21],
        ];
        yield [__DIR__ . '/Fixture/ControlWithForm.php', $errorMessages];

        $errorMessages = [
            [
                'Method ' . ControlWithHandle::class . '::handleDoSomething() invoked with 3 parameters, 1-2 required.',
                18,
            ],
            [
                'Parameter #2 $bar of method ' . ControlWithHandle::class . '::handleDoSomething() expects array|null, string given.',
                18,
            ],
            [
                'Parameter #2 $bar of method ' . ControlWithHandle::class . '::handleDoSomething() expects array|null, string given.',
                18,
            ],
            ['Call to an undefined method ' . ControlWithHandle::class . '::handleUnknown().', 18],
            [
                'Parameter #2 $add of method ' . FooPresenter::class . '::renderDefault() expects array|null, string given.',
                18,
            ],
        ];
        yield [__DIR__ . '/Fixture/ControlWithHandle.php', $errorMessages];
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
