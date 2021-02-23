<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoInheritanceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoInheritanceRule;

final class NoInheritanceRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeClassWithParent.php', [[NoInheritanceRule::ERROR_MESSAGE, 9]]];
        yield [__DIR__ . '/Fixture/SkipTestCase.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoInheritanceRule::class, __DIR__ . '/../../../config/symplify-rules.neon');
    }
}
