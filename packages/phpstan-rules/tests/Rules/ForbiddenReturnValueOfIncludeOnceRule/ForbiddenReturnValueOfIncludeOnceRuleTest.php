<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenReturnValueOfIncludeOnceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenReturnValueOfIncludeOnceRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenReturnValueOfIncludeOnceRule>
 */
final class ForbiddenReturnValueOfIncludeOnceRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array<string|int[]|string[]>>
     */
    public function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/ReturnRequireOnce.php',
            [[ForbiddenReturnValueOfIncludeOnceRule::ERROR_MESSAGE, 11]],
        ];
        yield [
            __DIR__ . '/Fixture/AssignRequireOnce.php',
            [[ForbiddenReturnValueOfIncludeOnceRule::ERROR_MESSAGE, 11]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenReturnValueOfIncludeOnceRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
