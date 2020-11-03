<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenConstructorDependencyByTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Psr\Container\ContainerInterface;
use Symplify\CodingStandard\Rules\ForbiddenConstructorDependencyByTypeRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenConstructorDependencyByTypeRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<mixed>
     */
    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNoConstruct.php', []];
        yield [__DIR__ . '/Fixture/SkipNoConstructParameter.php', []];
        yield [__DIR__ . '/Fixture/PassContainertoConstructorParameter.php', [
            [sprintf(ForbiddenConstructorDependencyByTypeRule::ERROR_MESSAGE, ContainerInterface::class), 9],
        ],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenConstructorDependencyByTypeRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
