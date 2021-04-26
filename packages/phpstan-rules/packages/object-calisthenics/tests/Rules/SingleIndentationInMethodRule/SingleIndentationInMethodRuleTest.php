<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\SingleIndentationInMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\SingleIndentationInMethodRule;

final class SingleIndentationInMethodRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(SingleIndentationInMethodRule::ERROR_MESSAGE, 1);
        yield [__DIR__ . '/Fixture/ManyIndentations.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/SkipSingleIndentation.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            SingleIndentationInMethodRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
