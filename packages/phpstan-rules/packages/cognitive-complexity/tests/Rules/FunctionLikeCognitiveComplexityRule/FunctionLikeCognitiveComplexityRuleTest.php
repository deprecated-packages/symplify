<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\Tests\Rules\FunctionLikeCognitiveComplexityRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule;
use Symplify\PHPStanRules\CognitiveComplexity\Tests\Rules\FunctionLikeCognitiveComplexityRule\Fixture\ClassMethodOverComplicated;
use Symplify\PHPStanRules\CognitiveComplexity\Tests\Rules\FunctionLikeCognitiveComplexityRule\Fixture\VideoRepository;

/**
 * @extends AbstractServiceAwareRuleTestCase<FunctionLikeCognitiveComplexityRule>
 */
final class FunctionLikeCognitiveComplexityRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideDataForTest()
     * @param array<int, array<int|string>> $expectedErrorMessagesWithLines
     */
    public function test(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideDataForTest(): Iterator
    {
        $errorMessage = sprintf(FunctionLikeCognitiveComplexityRule::ERROR_MESSAGE, 'someFunction()', 9, 8);
        yield [__DIR__ . '/Fixture/function.php.inc', [[$errorMessage, 3]]];

        $errorMessage = sprintf(
            FunctionLikeCognitiveComplexityRule::ERROR_MESSAGE,
            ClassMethodOverComplicated::class . '::someMethod()',
            9,
            8
        );
        yield [__DIR__ . '/Fixture/ClassMethodOverComplicated.php', [[$errorMessage, 7]]];

        $errorMessage = sprintf(
            FunctionLikeCognitiveComplexityRule::ERROR_MESSAGE,
            VideoRepository::class . '::findBySlug()',
            9,
            8
        );
        yield [__DIR__ . '/Fixture/VideoRepository.php', [[$errorMessage, 12]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            FunctionLikeCognitiveComplexityRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
