<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\Tests\Rules\ClassLikeCognitiveComplexityRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule;

final class ClassLikeCognitiveComplexityRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideDataForTest(): Iterator
    {
        $errorMessage = sprintf(
            ClassLikeCognitiveComplexityRule::ERROR_MESSAGE,
            'Class',
            'ClassWithManyComplexMethods',
            54,
            50
        );

        yield [__DIR__ . '/Source/ClassWithManyComplexMethods.php', [[$errorMessage, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ClassLikeCognitiveComplexityRule::class,
            __DIR__ . '/../../../../../packages/cognitive-complexity/config/cognitive-complexity-rules.neon'
        );
    }
}
