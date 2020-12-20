<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\PreventDuplicateClassMethodRule;

final class PreventDuplicateClassMethodRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @param string[] $filePaths
     * @dataProvider provideData()
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/FirstClass.php'], []];
        yield [[__DIR__ . '/Fixture/ValueObject1.php', __DIR__ . '/Fixture/ValueObject2.php'], []];
        yield [[__DIR__ . '/Fixture/Entity1.php', __DIR__ . '/Fixture/Entity2.php'], []];

        yield [[__DIR__ . '/Fixture/AnInterface.php'], []];

        yield [[
            __DIR__ . '/Fixture/ClassWithTrait.php',
            __DIR__ . '/Fixture/TraitUsingTrait.php',
            __DIR__ . '/Fixture/SomeTrait.php',
        ], []];

        $errorMessage = sprintf(
            PreventDuplicateClassMethodRule::ERROR_MESSAGE,
            'someMethod',
            'Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture\FirstClass'
        );
        yield [[__DIR__ . '/Fixture/SecondClassDuplicateFirstClassMethod.php'], [[$errorMessage, 15]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            PreventDuplicateClassMethodRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
