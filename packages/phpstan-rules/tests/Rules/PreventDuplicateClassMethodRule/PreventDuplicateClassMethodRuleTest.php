<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\PreventDuplicateClassMethodRule;
use Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture\FirstClass;

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
        yield [[__DIR__ . '/Fixture/ValueObject/SkipChair.php', __DIR__ . '/Fixture/ValueObject/SkipTable.php'], []];
        yield [[__DIR__ . '/Fixture/Entity/SkipApple.php', __DIR__ . '/Fixture/Entity/SkipCar.php'], []];

        yield [[__DIR__ . '/Fixture/SkipInterface.php'], []];

        yield [[
            __DIR__ . '/Fixture/SkipClassWithTrait.php',
            __DIR__ . '/Fixture/SkipTraitUsingTrait.php',
            __DIR__ . '/Fixture/SkipSomeTrait.php',
        ], []];

        $errorMessage = sprintf(PreventDuplicateClassMethodRule::ERROR_MESSAGE, 'someMethod', FirstClass::class);
        yield [[
            __DIR__ . '/Fixture/FirstClass.php',
            __DIR__ . '/Fixture/SecondClassDuplicateFirstClassMethod.php',
        ], [[$errorMessage, 15]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            PreventDuplicateClassMethodRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
