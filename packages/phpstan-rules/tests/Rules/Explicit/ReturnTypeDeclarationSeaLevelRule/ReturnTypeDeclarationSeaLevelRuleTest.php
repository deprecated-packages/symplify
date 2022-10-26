<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ReturnTypeDeclarationSeaLevelRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Collector\FunctionLike\ReturnTypeSeaLevelCollector;
use Symplify\PHPStanRules\Rules\Explicit\ReturnTypeDeclarationSeaLevelRule;

/**
 * @extends RuleTestCase<ReturnTypeDeclarationSeaLevelRule>
 */
final class ReturnTypeDeclarationSeaLevelRuleTest extends RuleTestCase
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
        yield [[__DIR__ . '/Fixture/SkipKnownReturnType.php', __DIR__ . '/Fixture/SkipAgainKnownReturnType.php'], []];
        yield [[__DIR__ . '/Fixture/SkipConstructor.php'], []];

        $errorMessage = sprintf(ReturnTypeDeclarationSeaLevelRule::ERROR_MESSAGE, 2, 0, 80);
        yield [[__DIR__ . '/Fixture/UnknownReturnType.php'], [[$errorMessage, -1]]];
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
        return self::getContainer()->getByType(ReturnTypeDeclarationSeaLevelRule::class);
    }

    /**
     * @return Collector[]
     */
    protected function getCollectors(): array
    {
        $paramTypeSeaLevelCollector = self::getContainer()->getByType(ReturnTypeSeaLevelCollector::class);

        return [$paramTypeSeaLevelCollector];
    }
}
