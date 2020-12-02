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
        yield [__DIR__ . '/Fixture/Controller/InControllerNamespace.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckControllerRepositoryLayerRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
