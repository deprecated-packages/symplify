<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\NoParentMethodCallOnNoOverrideProcessRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class NoParentMethodCallOnNoOverrideProcessRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/NotCallParentMethod.php', []];
        yield [__DIR__ . '/Fixture/ParentMethodCallOverride.php', []];
        yield [__DIR__ . '/Fixture/ParentMethodCallInsideExpression.php', []];
        yield [__DIR__ . '/Fixture/ParentMethodCallFromDifferentMethodName.php', []];
        yield [
            __DIR__ . '/Fixture/ParentMethodCallNoOverride.php',
            [[NoParentMethodCallOnNoOverrideProcessRule::ERROR_MESSAGE, 11]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoParentMethodCallOnNoOverrideProcessRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
