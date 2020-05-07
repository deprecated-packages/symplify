<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\Tests\Rules\FunctionLikeCognitiveComplexityRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule;
use Symplify\CodingStandard\CognitiveComplexity\Tests\Rules\FunctionLikeCognitiveComplexityRule\Source\ClassMethodOverComplicated;
use Symplify\CodingStandard\CognitiveComplexity\Tests\Rules\FunctionLikeCognitiveComplexityRule\Source\VideoRepository;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class FunctionLikeCognitiveComplexityRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(FunctionLikeCognitiveComplexityRule::ERROR_MESSAGE, 'someFunction()', 9, 8);
        yield [__DIR__ . '/Source/function.php.inc', [[$errorMessage, 3]]];

        $errorMessage = sprintf(
            FunctionLikeCognitiveComplexityRule::ERROR_MESSAGE,
            ClassMethodOverComplicated::class . '::someMethod()',
            9,
            8
        );
        yield [__DIR__ . '/Source/ClassMethodOverComplicated.php', [[$errorMessage, 7]]];

        $errorMessage = sprintf(
            FunctionLikeCognitiveComplexityRule::ERROR_MESSAGE,
            VideoRepository::class . '::findBySlug()',
            9,
            8
        );
        yield [__DIR__ . '/Source/VideoRepository.php', [[$errorMessage, 12]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            FunctionLikeCognitiveComplexityRule::class,
            __DIR__ . '/../../../../../config/symplify-rules.neon'
        );
    }
}
