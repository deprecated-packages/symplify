<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckTypehintCallerTypeRule;

final class CheckTypehintCallerTypeRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNotFromThis.php', []];
        yield [__DIR__ . '/Fixture/SkipParentNotIf.php', []];
        yield [__DIR__ . '/Fixture/SkipNoArgs.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckTypehintCallerTypeRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
