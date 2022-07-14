<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule;

use Iterator;
use PhpParser\Node\Param;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule;

/**
 * @extends RuleTestCase<NarrowPublicClassMethodParamTypeByCallerTypeRule>
 */
final class NarrowPublicClassMethodParamTypeByCallerTypeRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     *
     * @param string[] $filePaths
     * @param mixed[] $expectedErrorsWithLines
     */
    public function testRule(array $filePaths, array $expectedErrorsWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SkipNonPublicClassMethod.php'], []];

        // skip expected scalar type
        yield [[
            __DIR__ . '/Fixture/SkipProperlyFilledParamType.php',
            __DIR__ . '/Source/ExpectedType/FirstTypedCaller.php',
            __DIR__ . '/Source/ExpectedType/SecondTypedCaller.php',
        ], []];

        // skip expected object type
        yield [[
            __DIR__ . '/Fixture/SkipExpectedClassType.php',
            __DIR__ . '/Source/ExpectedClassType/FirstClassTypedCaller.php',
            __DIR__ . '/Source/ExpectedClassType/SecondClassTypedCaller.php',
        ], []];

        // skip class-string
        yield [[
            __DIR__ . '/Fixture/SkipClassStringPassed.php',
            __DIR__ . '/Source/ExpectedClassString/FirstTypedCaller.php',
            __DIR__ . '/Source/ExpectedClassString/SecondTypedCaller.php',
        ], []];

        // skip everything in case of values is mixed
        yield [[
            __DIR__ . '/Fixture/SkipMixedAndString.php',
            __DIR__ . '/Source/MixedAndString/FirstCaller.php',
            __DIR__ . '/Source/MixedAndString/SecondCaller.php',
        ], []];

        // skip int + string values
        yield [[
            __DIR__ . '/Fixture/SkipMixedAndString.php',
            __DIR__ . '/Source/MixedAndString/FirstCaller.php',
            __DIR__ . '/Source/MixedAndString/ThirdCaller.php',
        ], []];

        // skip nullable compare
        yield [[
            __DIR__ . '/Fixture/SkipNullableCompare.php',
            __DIR__ . '/Source/NullableParam/FirstNullable.php',
            __DIR__ . '/Source/NullableParam/SecondNullable.php',
        ], []];

        // skip api
        yield [[
            __DIR__ . '/Fixture/SkipApiMarked.php',
            __DIR__ . '/Source/ExpectedNodeApi/CallWithProperty.php',
        ], []];

        $argErrorMessage = sprintf(NarrowPublicClassMethodParamTypeByCallerTypeRule::ERROR_MESSAGE, 'int');
        yield [[
            __DIR__ . '/Fixture/PublicDoubleShot.php',
            __DIR__ . '/Source/FirstCaller.php',
            __DIR__ . '/Source/SecondCaller.php',
        ], [[$argErrorMessage, 9]]];
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
        return self::getContainer()->getByType(NarrowPublicClassMethodParamTypeByCallerTypeRule::class);
    }

    /**
     * Warning, just spent hour looking for why the test does not run :D This should be implicit part of the parent
     * class.
     *
     * @return Collector[]
     */
    protected function getCollectors(): array
    {
        return self::getContainer()->getServicesByTag('phpstan.collector');
    }
}
