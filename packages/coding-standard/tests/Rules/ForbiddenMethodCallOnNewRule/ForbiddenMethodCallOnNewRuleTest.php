<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodCallOnNewRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenMethodCallOnNewRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenMethodCallOnNewRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/MethodCallOnVariable.php', []];
        yield [__DIR__ . '/Fixture/MethodCallOnNew.php', [[ForbiddenMethodCallOnNewRule::ERROR_MESSAGE, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenMethodCallOnNewRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
