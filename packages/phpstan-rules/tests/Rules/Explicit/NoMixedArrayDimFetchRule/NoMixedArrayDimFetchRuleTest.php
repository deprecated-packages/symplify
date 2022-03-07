<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedArrayDimFetchRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoMixedArrayDimFetchRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoMixedArrayDimFetchRule>
 */
final class NoMixedArrayDimFetchRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[]|array<int, array<int|string>> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(NoMixedArrayDimFetchRule::ERROR_MESSAGE, '$this->items');
        yield [__DIR__ . '/Fixture/ReportUntypedArray.php', [[$errorMessage, 13]]];

        yield [__DIR__ . '/Fixture/SkipTypedArray.php', []];
        yield [__DIR__ . '/Fixture/SkipString.php', []];
<<<<<<< HEAD
        yield [__DIR__ . '/Fixture/SkipExternalPhpParser.php', []];
=======
>>>>>>> [PHPStanRules] Skip string in NoMixedArrayDimFetchRule
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoMixedArrayDimFetchRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
