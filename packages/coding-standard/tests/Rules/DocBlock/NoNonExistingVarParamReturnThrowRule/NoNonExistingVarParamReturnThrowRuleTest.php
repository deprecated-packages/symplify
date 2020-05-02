<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\DocBlock\NoNonExistingVarParamReturnThrowRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use RuntimeException;
use Symplify\CodingStandard\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule;
use Symplify\CodingStandard\Rules\DocBlock\NoNonExistingVarParamReturnThrowRule;
use Symplify\CodingStandard\Tests\PHPStan\Testing\AbstractServiceAwareRuleTestCase;

final class NoNonExistingVarParamReturnThrowRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], [$expectedErrorMessagesWithLines]);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(NoNonExistingVarParamReturnThrowRule::ERROR_MESSAGE, RuntimeException::class);
        yield [__DIR__ . '/Fixture/NonExistingVarType.php', [$errorMessage, 13]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoNonExistingVarParamReturnThrowRule::class,
            __DIR__ . '/../../../../config/symplify-rules.neon'
        );
    }
}
