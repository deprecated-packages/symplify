<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenProtectedPropertyRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenProtectedPropertyRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenProtectedPropertyRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/HasNonProtectedPropertyAndConstant.php', []];
        yield [__DIR__ . '/Fixture/AbstractClassWithConstructorInjection.php', []];
        yield [__DIR__ . '/Fixture/AbstractKernelTestCase.php', []];
        yield [__DIR__ . '/Fixture/HasProtectedPropertyAndConstant.php',
            [
                [ForbiddenProtectedPropertyRule::ERROR_MESSAGE, 11],
                [ForbiddenProtectedPropertyRule::ERROR_MESSAGE, 15],
                [ForbiddenProtectedPropertyRule::ERROR_MESSAGE, 19],
            ],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenProtectedPropertyRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
