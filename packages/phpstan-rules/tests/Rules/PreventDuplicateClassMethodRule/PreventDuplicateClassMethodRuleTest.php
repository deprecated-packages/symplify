<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\PreventDuplicateClassMethodRule;
use Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture\DifferentMethodName1;

final class PreventDuplicateClassMethodRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @param string[] $filePaths
     * @param array<int|string> $expectedErrorMessagesWithLines
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
        yield [[__DIR__ . '/Fixture/SkipConstruct.php', __DIR__ . '/Fixture/SkipAnotherConstruct.php'], []];
        yield [[__DIR__ . '/Fixture/SkipTest.php', __DIR__ . '/Fixture/SkipAnotherTest.php'], []];

        yield [[__DIR__ . '/Fixture/SkipNodeType.php'], []];
        yield [[__DIR__ . '/Fixture/SkipDoubleStmt.php'], []];

        yield [[
            __DIR__ . '/Fixture/SkipClassWithTrait.php',
            __DIR__ . '/Fixture/SkipTraitUsingTrait.php',
            __DIR__ . '/Fixture/SkipSomeTrait.php',
        ], []];

        yield [[
            __DIR__ . '/Fixture/SkipSomeTrait.php',
            __DIR__ . '/Fixture/SkipClassUseTrait1.php',
            __DIR__ . '/Fixture/SkipClassUseTrait2.php',
        ], []];

        $errorMessage = sprintf(
            PreventDuplicateClassMethodRule::ERROR_MESSAGE,
            'sleep',
            'go',
            DifferentMethodName1::class
        );

        yield [[
            __DIR__ . '/Fixture/DifferentMethodName1.php',
            __DIR__ . '/Fixture/DifferentMethodName2.php',
        ], [[$errorMessage, 9]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            PreventDuplicateClassMethodRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
