<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckRequiredMethodTobeAutowireWithClassNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\CheckRequiredMethodTobeAutowireWithClassNameRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class CheckRequiredMethodTobeAutowireWithClassNameRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/EmptyDocblock.php', []];
        yield [__DIR__ . '/Fixture/WithoutRequired.php', []];
        yield [__DIR__ . '/Fixture/WithRequiredAutowire.php', []];
        yield [
            __DIR__ . '/Fixture/WithRequiredNotAutowire.php',
            [[CheckRequiredMethodTobeAutowireWithClassNameRule::ERROR_MESSAGE, 12]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckRequiredMethodTobeAutowireWithClassNameRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
