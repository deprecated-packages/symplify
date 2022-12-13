<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Collector\ClassMethod\MethodCallCollector;
use Symplify\PHPStanRules\Collector\ClassMethod\PublicClassMethodCollector;
use Symplify\PHPStanRules\Rules\DeadCode\UnusedPublicClassMethodRule;

/**
 * @extends RuleTestCase<UnusedPublicClassMethodRule>
 */
final class UnusedPublicClassMethodRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param string[] $filePaths
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(UnusedPublicClassMethodRule::ERROR_MESSAGE, 'runHere');
        yield [[__DIR__ . '/Fixture/LocallyUsedPublicMethod.php'],
            [[$errorMessage, 14, UnusedPublicClassMethodRule::TIP_MESSAGE]], ];

        $errorMessage = sprintf(UnusedPublicClassMethodRule::ERROR_MESSAGE, 'extraMethod');
        yield [[__DIR__ . '/Fixture/InterfaceWithExtraMethod.php'],
            [[$errorMessage, 15, UnusedPublicClassMethodRule::TIP_MESSAGE]], ];

        // public methods expected
        yield [[__DIR__ . '/Fixture/SkipTestPublicMethod.php'], []];
        yield [[__DIR__ . '/Fixture/SkipControllerMethod.php'], []];

        // method required by parent
        yield [[__DIR__ . '/Fixture/SkipParentMethodOverride.php'], []];
        yield [[__DIR__ . '/Fixture/SkipImplementsInterfaceCoveredByContract.php'], []];

        yield [[__DIR__ . '/Fixture/SkipClassWithAttribute.php'], []];
        yield [[__DIR__ . '/Fixture/SkipStaticPublicMethod.php'], []];
        yield [[__DIR__ . '/Fixture/SkipPublicApiClassMethod.php'], []];
        yield [[__DIR__ . '/Fixture/SkipInterfaceMethod.php'], []];
        yield [[__DIR__ . '/Fixture/SkipPrivateClassMethod.php'], []];
        yield [[__DIR__ . '/Fixture/SkipUsedPublicMethod.php', __DIR__ . '/Source/ClassMethodCaller.php'], []];

        yield [[
            __DIR__ . '/Fixture/SkipNullableUsedPublicMethod.php', __DIR__ . '/Source/NullableClassMethodCaller.php', ],
            [],
        ];

        // parent abstract method used by child call
        yield [[
            __DIR__ . '/Fixture/SkipChildUsedPublicMethod.php',
            __DIR__ . '/Source/Repository/AbstractRepository.php',
            __DIR__ . '/Source/Repository/ChildRepository.php',
        ], []];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    /**
     * @return array<Collector>
     */
    protected function getCollectors(): array
    {
        $publicClassMethodCollector = self::getContainer()->getByType(PublicClassMethodCollector::class);
        $methodCallCollector = self::getContainer()->getByType(MethodCallCollector::class);

        return [$methodCallCollector, $publicClassMethodCollector];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(UnusedPublicClassMethodRule::class);
    }
}
