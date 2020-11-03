<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoConstructorInTestRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoConstructorInTestRule;

final class NoConstructorInTestRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeClass.php', []];
        yield [__DIR__ . '/Fixture/Test1/SomeTest.php', []];
        yield [__DIR__ . '/Fixture/Test2/SomeTest.php', [[NoConstructorInTestRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoConstructorInTestRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
