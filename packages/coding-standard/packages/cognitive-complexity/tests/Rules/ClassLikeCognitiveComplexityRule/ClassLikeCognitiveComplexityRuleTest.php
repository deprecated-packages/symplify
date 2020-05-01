<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\Tests\Rules\ClassLikeCognitiveComplexityRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule;
use Symplify\CodingStandard\Tests\PHPStan\Testing\AbstractServiceAwareRuleTestCase;

final class ClassLikeCognitiveComplexityRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file, ?array $expectedMessagesWithLines = null): void
    {
        $this->analyse([$file], $expectedMessagesWithLines);
    }

    public function provideDataForTest(): Iterator
    {
        yield [
            __DIR__ . '/Source/ClassWithManyComplexMethods.php',
            [['Cognitive complexity for "ClassWithManyComplexMethods" class is 54, keep it under 50', 7]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ClassLikeCognitiveComplexityRule::class,
            __DIR__ . '/../../../../../config/symplify-rules.neon'
        );
    }
}
