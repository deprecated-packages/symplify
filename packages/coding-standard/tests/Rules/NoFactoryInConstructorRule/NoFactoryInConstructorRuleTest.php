<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoFactoryInConstructorRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\NoFactoryInConstructorRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class NoFactoryInConstructorRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/WithoutConstructor.php', []];
        yield [__DIR__ . '/Fixture/WithConstructor.php', []];
        yield [__DIR__ . '/Fixture/WithConstructorWithoutFactory.php', []];
        yield [__DIR__ . '/Fixture/WithConstructorUseMethodCallFromCurrentObject.php', []];
        yield [__DIR__ . '/Fixture/WithConstructorWithFactory.php', [[NoFactoryInConstructorRule::ERROR_MESSAGE, 16]]];
        yield [
            __DIR__ . '/Fixture/WithConstructorWithFactoryWithAssignment.php',
            [[NoFactoryInConstructorRule::ERROR_MESSAGE, 16]],
        ];
        yield [
            __DIR__ . '/Fixture/WithConstructorWithFactoryWithMutliAssignment.php',
            [[NoFactoryInConstructorRule::ERROR_MESSAGE, 16]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoFactoryInConstructorRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
