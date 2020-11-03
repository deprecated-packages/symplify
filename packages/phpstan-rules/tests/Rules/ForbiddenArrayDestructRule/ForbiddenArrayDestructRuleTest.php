<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayDestructRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenArrayDestructRule;

final class ForbiddenArrayDestructRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/ClassWithArrayDestruct.php', [[ForbiddenArrayDestructRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/SkipSwap.php', []];
        yield [__DIR__ . '/Fixture/SkipExplode.php', []];
        yield [__DIR__ . '/Fixture/SkipStringsSplit.php', []];
        yield [__DIR__ . '/Fixture/SkipExternalReturnArray.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenArrayDestructRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
