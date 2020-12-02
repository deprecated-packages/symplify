<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckControllerRepositoryLayerRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckControllerRepositoryLayerRule;

final class CheckControllerRepositoryLayerRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/Form.php', []];
        yield [__DIR__ . '/Fixture/NotControllerRepositoryWithExtends.php', []];
        yield [__DIR__ . '/Fixture/Controller/NoDependency.php', []];
        yield [__DIR__ . '/Fixture/Controller/WithRepositoryDependency.php', []];
        yield [__DIR__ . '/Fixture/Controller/WithFormDependency.php', []];
        yield [__DIR__ . '/Fixture/Controller/WithEntityManagerDependency.php', [
            [
                sprintf(CheckControllerRepositoryLayerRule::ERROR_MESSAGE, 'Controller', 'EntityManager', 'Repository'),
                7
            ],
        ]];

        yield [__DIR__ . '/Fixture/Repository/NoDependency.php', []];
        yield [__DIR__ . '/Fixture/Repository/WithEntityManagerDependency.php', []];
        yield [__DIR__ . '/Fixture/Repository/WithFormDependency.php', [
            [
                sprintf(CheckControllerRepositoryLayerRule::ERROR_MESSAGE, 'Repository', 'form', 'EntityManager'),
                7
            ],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckControllerRepositoryLayerRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
