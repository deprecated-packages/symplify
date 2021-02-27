<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoModifyAndReturnSelfObjectRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoModifyAndReturnSelfObjectRule;

final class NoModifyAndReturnSelfObjectRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNotReturnObject.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoModifyAndReturnSelfObjectRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
