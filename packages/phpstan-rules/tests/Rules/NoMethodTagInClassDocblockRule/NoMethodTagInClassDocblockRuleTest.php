<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMethodTagInClassDocblockRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoMethodTagInClassDocblockRule;

final class NoMethodTagInClassDocblockRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/ClassWithNoDocblock.php', []];
        yield [__DIR__ . '/Fixture/ClassWithNoMethodTag.php', []];
        yield [__DIR__ . '/Fixture/ClassWithMethodTag.php', [[NoMethodTagInClassDocblockRule::ERROR_MESSAGE, 10]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoMethodTagInClassDocblockRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
