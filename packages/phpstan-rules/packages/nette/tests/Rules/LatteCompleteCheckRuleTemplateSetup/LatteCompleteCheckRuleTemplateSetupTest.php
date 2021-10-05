<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRuleTemplateSetup;

use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Nette\Rules\LatteCompleteCheckRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<LatteCompleteCheckRule>
 */
final class LatteCompleteCheckRuleTemplateSetupTest extends AbstractServiceAwareRuleTestCase
{
    public function testRule(): void
    {
        $fileToClass = [
            __DIR__ . '/Fixtures/PropertyReadTemplate/ExampleControl.php' => \Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRuleTemplateSetup\Fixtures\PropertyReadTemplate\ExampleControl::class,
            __DIR__ . '/Fixtures/NoAdditionalPropertyRead/ExampleControl.php' => \Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRuleTemplateSetup\Fixtures\NoAdditionalPropertyRead\ExampleControl::class,
            __DIR__ . '/Fixtures/GetTemplateAndReplaceExtension/ExampleControl.php' => \Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRuleTemplateSetup\Fixtures\GetTemplateAndReplaceExtension\ExampleControl::class,
            __DIR__ . '/Fixtures/RenderWithParameters/ExampleControl.php' => \Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRuleTemplateSetup\Fixtures\RenderWithParameters\ExampleControl::class,
            __DIR__ . '/Fixtures/TemplateAsVariableAndRenderToStringWithParameters/ExampleControl.php' => \Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRuleTemplateSetup\Fixtures\TemplateAsVariableAndRenderToStringWithParameters\ExampleControl::class,
        ];

        foreach ($fileToClass as $file => $class) {
            $this->analyse([$file], [
//                [
//                    'Static method Latte\Runtime\Filters::date() invoked with 3 parameters, 1-2 required.',
//                    2
//                ],
//                [
//                    'Parameter #2 $format of static method Latte\Runtime\Filters::date() expects string|null, int given.',
//                    2
//                ],
                [
                    'Variable $nonExistingVariable might not be defined.',
                    3
                ],
                [
                    'Call to an undefined method Nette\Security\User::nonExistingMethod().',
                    6
                ],
                [
                    'Call to an undefined method Symplify\PHPStanRules\Nette\Tests\Rules\LatteCompleteCheckRuleTemplateSetup\Source\ExampleModel::getTitle().',
                    9
                ],
                [
                    'Method ' . $class . '::render() invoked with 2 parameters, 0-1 required.',
                    12
                ],
                [
                    'Parameter #1 $param of method ' . $class . '::render() expects int|null, string given.',
                    12
                ],
            ]);
        }
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(LatteCompleteCheckRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
