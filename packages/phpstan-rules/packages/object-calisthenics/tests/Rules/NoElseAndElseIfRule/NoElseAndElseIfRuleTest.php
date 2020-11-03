<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\NoElseAndElseIfRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\NoElseAndElseIfRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class NoElseAndElseIfRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeElse.php', [[NoElseAndElseIfRule::MESSAGE, 13]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoElseAndElseIfRule::class,
            __DIR__ . '/../../../../../packages/object-calisthenics/config/object-calisthenics-rules.neon'
        );
    }
}
