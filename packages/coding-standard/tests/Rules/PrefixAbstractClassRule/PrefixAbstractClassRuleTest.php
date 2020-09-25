<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PrefixAbstractClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\PrefixAbstractClassRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class PrefixAbstractClassRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/AbstractSomeAbstractClass.php', []];

        $errorMessage = sprintf(PrefixAbstractClassRule::ERROR_MESSAGE, 'SomeAbstractClass');
        yield [__DIR__ . '/Fixture/SomeAbstractClass.php', [[$errorMessage, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            PrefixAbstractClassRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
