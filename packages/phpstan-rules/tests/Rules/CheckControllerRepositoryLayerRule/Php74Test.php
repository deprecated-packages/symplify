<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckControllerRepositoryLayerRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckControllerRepositoryLayerRule;

/**
 * @requires PHP 7.4
 */
final class Php74Test extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/FixturePhp74/Controller/WithEntityManagerDependency.php', [
            [
                sprintf(CheckControllerRepositoryLayerRule::ERROR_MESSAGE, 'Controller', 'EntityManager', 'Repository'),
                7,
            ],
        ]];

        yield [__DIR__ . '/FixturePhp74/Repository/WithFormDependency.php', [
            [sprintf(CheckControllerRepositoryLayerRule::ERROR_MESSAGE, 'Repository', 'Form', 'EntityManager'), 7],
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
