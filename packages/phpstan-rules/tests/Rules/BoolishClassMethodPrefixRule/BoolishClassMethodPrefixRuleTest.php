<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\BoolishClassMethodPrefixRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\BoolishClassMethodPrefixRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

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
        $firstErrorMessage = sprintf(BoolishClassMethodPrefixRule::ERROR_MESSAGE, 'honesty');
        $secondErrorMessage = sprintf(BoolishClassMethodPrefixRule::ERROR_MESSAGE, 'thatWasGreat');

        yield [
            __DIR__ . '/Fixture/ClassWithBoolishMethods.php',
            [[$firstErrorMessage, 9], [$secondErrorMessage, 14]],
        ];

        // no erros
        yield [__DIR__ . '/Fixture/ClassWithEmptyReturn.php', []];
        yield [__DIR__ . '/Fixture/ClassThatImplementsInterface.php', []];
        yield [__DIR__ . '/Fixture/SkipRequiredByInterface.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            BoolishClassMethodPrefixRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
