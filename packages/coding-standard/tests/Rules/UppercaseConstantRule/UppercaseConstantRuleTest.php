<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\UppercaseConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\UppercaseConstantRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class UppercaseConstantRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(UppercaseConstantRule::ERROR_MESSAGE, 'SMall');
        yield [__DIR__ . '/Fixture/ConstantLower.php', [[$errorMessage, 9]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            UppercaseConstantRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
