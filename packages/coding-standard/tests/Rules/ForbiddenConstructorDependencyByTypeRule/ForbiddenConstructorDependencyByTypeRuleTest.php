<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenConstructorDependencyByTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenConstructorDependencyByTypeRule;
use Symplify\CodingStandard\Tests\Rules\ForbiddenConstructorDependencyByTypeRule\Fixture\SkipNoConstruct;
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
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ForbiddenConstructorDependencyByTypeRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
