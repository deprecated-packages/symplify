<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ExclusiveDependencyRule;

use Doctrine\ORM\EntityManager;
use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ExclusiveDependencyRule;

final class ExclusiveDependencyRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNotSpecified.php', []];
        yield [__DIR__ . '/Fixture/SkipSomeRepository.php', []];

        $errorMessage = sprintf(ExclusiveDependencyRule::ERROR_MESSAGE, EntityManager::class, '*Repository');

        yield [__DIR__ . '/Fixture/SomeController.php', [[$errorMessage, 9]]];
        yield [__DIR__ . '/Fixture/WarnController.php', [[$errorMessage, 16]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ExclusiveDependencyRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
