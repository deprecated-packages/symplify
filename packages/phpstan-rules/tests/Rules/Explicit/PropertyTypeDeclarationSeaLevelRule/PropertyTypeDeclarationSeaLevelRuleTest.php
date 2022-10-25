<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\PropertyTypeDeclarationSeaLevelRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Collector\ClassLike\PropertyTypeSeaLevelCollector;
use Symplify\PHPStanRules\Rules\Explicit\PropertyTypeDeclarationSeaLevelRule;

/**
 * @extends RuleTestCase<PropertyTypeDeclarationSeaLevelRule>
 */
final class PropertyTypeDeclarationSeaLevelRuleTest extends RuleTestCase
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

    /**
     * @return Iterator<mixed>
     */
    public function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SkipKnownPropertyType.php'], []];

        $errorMessage = sprintf(PropertyTypeDeclarationSeaLevelRule::ERROR_MESSAGE, 2, 0, 80);
        yield [[__DIR__ . '/Fixture/UnknownPropertyType.php'], [[$errorMessage, -1]]];
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
        return self::getContainer()->getByType(PropertyTypeDeclarationSeaLevelRule::class);
    }

    /**
     * @return Collector[]
     */
    protected function getCollectors(): array
    {
        $propertyTypeSeaLevelCollector = self::getContainer()->getByType(PropertyTypeSeaLevelCollector::class);

        return [$propertyTypeSeaLevelCollector];
    }
}
