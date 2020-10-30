<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireNewArgumentConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\RequireNewArgumentConstantRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class RequireNewArgumentConstantRuleTest extends AbstractServiceAwareRuleTestCase
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
        return yield [__DIR__ . '/Fixture/SkippedInstance.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireNewArgumentConstantRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
