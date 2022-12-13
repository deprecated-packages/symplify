<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicStaticPropertyRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Collector\Class_\PublicStaticPropertyCollector;
use Symplify\PHPStanRules\Collector\StaticPropertyFetch\PublicStaticPropertyFetchCollector;
use Symplify\PHPStanRules\Rules\DeadCode\UnusedPublicStaticPropertyRule;

/**
 * @extends RuleTestCase<UnusedPublicStaticPropertyRule>
 */
final class UnusedPublicStaticPropertyRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(UnusedPublicStaticPropertyRule::ERROR_MESSAGE, 'somePublicStaticProperty');
        yield [
            [__DIR__ . '/Fixture/LocallyUsedStaticProperty.php'],
            [[$errorMessage, 7, UnusedPublicStaticPropertyRule::TIP_MESSAGE]],
        ];

        yield [[
            __DIR__ . '/Fixture/AnotherClassUsingPublicStaticProperty.php',
            __DIR__ . '/Source/SkipExternallyUsedPublicStaticProperty.php',
        ], []];

        yield [[__DIR__ . '/Fixture/SkipPrivateProperty.php'], []];

        yield [[__DIR__ . '/Fixture/SkipNonStaticProperty.php'], []];
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
        $container = self::getContainer();

        $publicStaticPropertyFetchCollector = $container->getByType(PublicStaticPropertyFetchCollector::class);
        $publicStaticPropertyCollector = $container->getByType(PublicStaticPropertyCollector::class);

        return [$publicStaticPropertyFetchCollector, $publicStaticPropertyCollector];
    }

    protected function getRule(): Rule
    {
        $container = self::getContainer();
        return $container->getByType(UnusedPublicStaticPropertyRule::class);
    }
}
