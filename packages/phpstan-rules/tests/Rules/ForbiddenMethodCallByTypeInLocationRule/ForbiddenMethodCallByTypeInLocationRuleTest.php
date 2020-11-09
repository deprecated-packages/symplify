<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallByTypeInLocationRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenMethodCallByTypeInLocationRule;

final class ForbiddenMethodCallByTypeInLocationRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<mixed>
     */
    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNotSpecified.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenMethodCallByTypeInLocationRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
