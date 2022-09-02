<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule;

/**
 * @extends RuleTestCase<ClassLikeCognitiveComplexityRule>
 */
final class ClassLikeCognitiveComplexityRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideDataForTest()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function test(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideDataForTest(): Iterator
    {
        $errorMessage = sprintf(ClassLikeCognitiveComplexityRule::ERROR_MESSAGE, 54, 50);
        yield [__DIR__ . '/Fixture/ClassWithManyComplexMethods.php', [[$errorMessage, 7]]];

        // comlexity: 9
        yield [__DIR__ . '/Fixture/SimpleCommand.php', []];
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
        return self::getContainer()->getByType(ClassLikeCognitiveComplexityRule::class);
    }
}
