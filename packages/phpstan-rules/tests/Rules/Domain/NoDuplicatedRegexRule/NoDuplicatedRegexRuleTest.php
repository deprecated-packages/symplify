<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\NoDuplicatedRegexRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Collector\ClassConst\RegexClassConstCollector;
use Symplify\PHPStanRules\Rules\Domain\NoDuplicatedRegexRule;

/**
 * @extends RuleTestCase<NoDuplicatedRegexRule>
 */
final class NoDuplicatedRegexRuleTest extends RuleTestCase
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
        $firstErrorMessage = sprintf(NoDuplicatedRegexRule::ERROR_MESSAGE, 'FIRST_REGEX', "#\d+#");
        $secondErrorMessage = sprintf(NoDuplicatedRegexRule::ERROR_MESSAGE, 'SECOND_REGEX', "#\d+#");

        yield [[__DIR__ . '/Fixture/DuplicatedRegexConst.php'], [
            [$firstErrorMessage, 9],
            [$secondErrorMessage, 11],
        ]];

        yield [[__DIR__ . '/Fixture/SkipUniqueRegexConst.php'], []];
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
        return self::getContainer()->getByType(NoDuplicatedRegexRule::class);
    }

    /**
     * @return Collector[]
     */
    protected function getCollectors(): array
    {
        $regexClassConstCollector = self::getContainer()->getByType(RegexClassConstCollector::class);
        return [$regexClassConstCollector];
    }
}
