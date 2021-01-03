<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\BoolishClassMethodPrefixRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\BoolishClassMethodPrefixRule;

final class BoolishClassMethodPrefixRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipClassWithEmptyReturn.php', []];
        yield [__DIR__ . '/Fixture/SkipClassThatImplementsInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipRequiredByInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipNestedCallback.php', []];

        $firstErrorMessage = sprintf(BoolishClassMethodPrefixRule::ERROR_MESSAGE, 'honesty');
        $secondErrorMessage = sprintf(BoolishClassMethodPrefixRule::ERROR_MESSAGE, 'thatWasGreat');

        yield [
            __DIR__ . '/Fixture/ClassWithBoolishMethods.php',
            [[$firstErrorMessage, 9], [$secondErrorMessage, 14]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            BoolishClassMethodPrefixRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
