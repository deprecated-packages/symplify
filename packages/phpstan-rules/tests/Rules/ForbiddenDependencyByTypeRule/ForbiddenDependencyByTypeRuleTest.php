<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenDependencyByTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Psr\Container\ContainerInterface;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenDependencyByTypeRule;

final class ForbiddenDependencyByTypeRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/PassContainerToConstructorParameter.php', [
            [sprintf(ForbiddenDependencyByTypeRule::ERROR_MESSAGE, ContainerInterface::class), 9],
        ],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenDependencyByTypeRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
