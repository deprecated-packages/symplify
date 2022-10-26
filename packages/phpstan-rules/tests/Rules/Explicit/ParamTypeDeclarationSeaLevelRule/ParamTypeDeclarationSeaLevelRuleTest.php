<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ParamTypeDeclarationSeaLevelRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Collector\FunctionLike\ParamTypeSeaLevelCollector;
use Symplify\PHPStanRules\Rules\Explicit\ParamTypeDeclarationSeaLevelRule;

/**
 * @extends RuleTestCase<ParamTypeDeclarationSeaLevelRule>
 */
final class ParamTypeDeclarationSeaLevelRuleTest extends RuleTestCase
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
        yield [[__DIR__ . '/Fixture/SkipKnownParamType.php', __DIR__ . '/Fixture/SkipAgainKnownParamType.php'], []];
        yield [[__DIR__ . '/Fixture/SkipVariadic.php'], []];
        yield [[__DIR__ . '/Fixture/SkipCallableParam.php'], []];

        $errorMessage = sprintf(ParamTypeDeclarationSeaLevelRule::ERROR_MESSAGE, 3, 0, 80);

        $errorMessage .= '

public function run($name, $age)
{
}

public function again($city)
{
}
';

        yield [[__DIR__ . '/Fixture/UnknownParamType.php'], [[$errorMessage, -1]]];
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
        return self::getContainer()->getByType(ParamTypeDeclarationSeaLevelRule::class);
    }

    /**
     * @return Collector[]
     */
    protected function getCollectors(): array
    {
        $paramTypeSeaLevelCollector = self::getContainer()->getByType(ParamTypeSeaLevelCollector::class);

        return [$paramTypeSeaLevelCollector];
    }
}
