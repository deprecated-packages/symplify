<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoProtectedElementInFinalClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoProtectedElementInFinalClassRule;

final class NoProtectedElementInFinalClassRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @param string[] $filePaths
     * @param array<int|string> $expectedErrorMessagesWithLines
     * @dataProvider provideData()
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SkipInterface.php'], []];
        yield [[__DIR__ . '/Fixture/SkipTrait.php'], []];
        yield [[__DIR__ . '/Fixture/SkipNotFinalClass.php'], []];

        yield [[__DIR__ . '/Fixture/SkipMicroKernelProtectedMethod.php'], []];
        yield [[__DIR__ . '/Fixture/SkipKernelProtectedMethod.php'], []];

        yield [
            [__DIR__ . '/Fixture/SomeFinalClassWithProtectedProperty.php'],
            [[NoProtectedElementInFinalClassRule::ERROR_MESSAGE, 9]],
        ];

        yield [
            [__DIR__ . '/Fixture/SomeFinalClassWithProtectedMethod.php'],
            [[NoProtectedElementInFinalClassRule::ERROR_MESSAGE, 9]],
        ];

        yield [
            [__DIR__ . '/Fixture/SomeFinalClassWithProtectedPropertyAndProtectedMethod.php'],
            [
                [NoProtectedElementInFinalClassRule::ERROR_MESSAGE, 9],
                [NoProtectedElementInFinalClassRule::ERROR_MESSAGE, 11],
            ],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoProtectedElementInFinalClassRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
