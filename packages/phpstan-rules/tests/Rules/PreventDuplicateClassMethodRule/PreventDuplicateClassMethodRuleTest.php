<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\PreventDuplicateClassMethodRule;
use Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture\DifferentMethodName1;

/**
 * @extends RuleTestCase<PreventDuplicateClassMethodRule>
 */
final class PreventDuplicateClassMethodRuleTest extends RuleTestCase
{
    /**
     * @param string[] $filePaths
     * @param mixed[] $expectedErrorMessagesWithLines
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

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(PreventDuplicateClassMethodRule::class);
    }
}
