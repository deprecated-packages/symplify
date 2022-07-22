<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoDuplicatedVariableCasingNameRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Collector\Variable\VariableNameCollector;
use Symplify\PHPStanRules\Rules\Explicit\NoDuplicatedVariableCasingNameRule;
use function sprintf;

/**
 * @extends RuleTestCase<NoDuplicatedVariableCasingNameRule>
 */
final class NoDuplicatedVariableCasingNameRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        $firstErrorMessage = sprintf(NoDuplicatedVariableCasingNameRule::ERROR_MESSAGE, 'value', 'value');
        $secondErrorMessage = sprintf(NoDuplicatedVariableCasingNameRule::ERROR_MESSAGE, 'value', 'valUE');

        yield [__DIR__ . '/Fixture/DifferentCasingNames.php', [
            [$firstErrorMessage, 11],
            [$secondErrorMessage, 13],
        ]];

        yield [__DIR__ . '/Fixture/SkipUnitedName.php', []];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    /**
     * @return Collector[]
     */
    protected function getCollectors(): array
    {
        return [new VariableNameCollector()];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoDuplicatedVariableCasingNameRule::class);
    }
}
