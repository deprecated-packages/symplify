<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\Tests\Rules\ClassLikeCognitiveComplexityRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ClassLikeCognitiveComplexityRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file, array $expectedMessagesWithLines): void
    {
        $this->analyse([$file], $expectedMessagesWithLines);
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
            __DIR__ . '/../../../../../config/symplify-rules.neon'
        );
    }
}
