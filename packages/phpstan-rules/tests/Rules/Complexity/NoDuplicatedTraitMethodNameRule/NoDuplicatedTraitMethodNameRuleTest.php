<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoDuplicatedTraitMethodNameRule;

use Iterator;
use PHPStan\Collectors\Collector;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Collector\ClassLike\TraitMethodNameCollector;
use Symplify\PHPStanRules\Rules\Complexity\NoDuplicatedTraitMethodNameRule;

/**
 * @extends RuleTestCase<\Symplify\PHPStanRules\Rules\Complexity\NoDuplicatedTraitMethodNameRule>
 */
final class NoDuplicatedTraitMethodNameRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     *
     * @param string[] $filePaths
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<int, mixed[]>
     */
    public function provideData(): Iterator
    {
        $errorMessage = sprintf(NoDuplicatedTraitMethodNameRule::ERROR_MESSAGE, 'run');
        yield [
            [__DIR__ . '/Fixture/ClassUsingOneTrait.php', __DIR__ . '/Fixture/ClassUsingSecondTrait.php'],
            [[$errorMessage, -1], [$errorMessage, -1]],
        ];

        yield [[__DIR__ . '/Fixture/ClassUsingOneTrait.php'], []];
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
        return [self::getContainer()->getByType(TraitMethodNameCollector::class)];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoDuplicatedTraitMethodNameRule::class);
    }
}
