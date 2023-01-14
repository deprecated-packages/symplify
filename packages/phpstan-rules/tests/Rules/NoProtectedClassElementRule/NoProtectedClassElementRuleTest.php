<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoProtectedClassElementRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\NoProtectedClassElementRule;

/**
 * @extends RuleTestCase<NoProtectedClassElementRule>
 */
final class NoProtectedClassElementRuleTest extends RuleTestCase
{
    /**
     * @param string[] $filePaths
     * @param mixed[] $expectedErrorMessagesWithLines
     * @dataProvider provideData()
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SkipAbstractTestCase.php'], []];
        yield [[__DIR__ . '/Fixture/SkipInterface.php'], []];
        yield [[__DIR__ . '/Fixture/SkipTrait.php'], []];

        yield [[__DIR__ . '/Fixture/SkipMicroKernelProtectedMethod.php'], []];
        yield [[__DIR__ . '/Fixture/SkipKernelProtectedMethod.php'], []];

        yield [
            [__DIR__ . '/Fixture/SomeFinalClassWithProtectedProperty.php'],
            [[NoProtectedClassElementRule::ERROR_MESSAGE, 9]],
        ];

        yield [
            [__DIR__ . '/Fixture/SomeFinalClassWithProtectedMethod.php'],
            [[NoProtectedClassElementRule::ERROR_MESSAGE, 9]],
        ];

        yield [
            [__DIR__ . '/Fixture/SomeFinalClassWithProtectedPropertyAndProtectedMethod.php'],
            [[NoProtectedClassElementRule::ERROR_MESSAGE, 9], [NoProtectedClassElementRule::ERROR_MESSAGE, 11]],
        ];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(NoProtectedClassElementRule::class);
    }
}
