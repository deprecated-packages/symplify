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
        yield [__DIR__ . '/Fixture/SkipNoParams.php', []];
        yield [__DIR__ . '/Fixture/SkipNoReturn.php', []];
        yield [__DIR__ . '/Fixture/SkipNoReturnNoExpr.php', []];
        yield [__DIR__ . '/Fixture/SkipReturnClone.php', []];

        yield [__DIR__ . '/Fixture/ModifyAndReturnSelfObject.php', [
            [NoModifyAndReturnSelfObjectRule::ERROR_MESSAGE, 11]
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoModifyAndReturnSelfObjectRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
