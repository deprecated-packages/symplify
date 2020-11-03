<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoContainerInjectionInConstructorRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoContainerInjectionInConstructorRule;

final class NoContainerInjectionInConstructorRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNonContainerClass.php', []];
        yield [__DIR__ . '/Fixture/SkipContainerBuilder.php', []];
        yield [
            __DIR__ . '/Fixture/WithContainerDependency.php',
            [[NoContainerInjectionInConstructorRule::ERROR_MESSAGE, 18]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoContainerInjectionInConstructorRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
