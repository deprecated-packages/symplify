<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\AllowedExclusiveDependencyRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\AllowedExclusiveDependencyRule;

final class AllowedExclusiveDependencyRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipSomeRepository.php', []];

        $errorMessage = sprintf(AllowedExclusiveDependencyRule::ERROR_MESSAGE, '*EntityManager', '*Repository');
        yield [__DIR__ . '/Fixture/SomeController.php', [[$errorMessage, 16]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            AllowedExclusiveDependencyRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
