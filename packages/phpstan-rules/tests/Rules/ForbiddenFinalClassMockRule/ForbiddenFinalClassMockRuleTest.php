<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenFinalClassMockRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenFinalClassMockRule;
use Symplify\PHPStanRules\Tests\Rules\ForbiddenFinalClassMockRule\Source\FinalClass;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenFinalClassMockRule>
 */
final class ForbiddenFinalClassMockRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|string[]|int[]> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(ForbiddenFinalClassMockRule::ERROR_MESSAGE, FinalClass::class);
        yield [__DIR__ . '/Fixture/MockOfFinalClass.php', [[$errorMessage, 14]]];
        yield [__DIR__ . '/Fixture/CreateMockMethod.php', [[$errorMessage, 14]]];

        yield [__DIR__ . '/Fixture/SkipNormalClass.php', []];
        yield [__DIR__ . '/Fixture/SkipCreateMockMethodOutsideTestCase.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenFinalClassMockRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
