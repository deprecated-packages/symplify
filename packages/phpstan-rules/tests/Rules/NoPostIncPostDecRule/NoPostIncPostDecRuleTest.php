<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoPostIncPostDecRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoPostIncPostDecRule;

final class NoPostIncPostDecRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/WithPostInc.php', [[NoPostIncPostDecRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/WithPostDec.php', [[NoPostIncPostDecRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoPostIncPostDecRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
