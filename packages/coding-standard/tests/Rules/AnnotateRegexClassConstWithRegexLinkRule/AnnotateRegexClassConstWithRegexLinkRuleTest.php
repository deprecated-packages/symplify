<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\AnnotateRegexClassConstWithRegexLinkRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\AnnotateRegexClassConstWithRegexLinkRule;

final class AnnotateRegexClassConstWithRegexLinkRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/ClassConstMissingLink.php',
            [[AnnotateRegexClassConstWithRegexLinkRule::ERROR_MESSAGE, 12]],
        ];

        yield [__DIR__ . '/Fixture/SkipShort.php', []];
        yield [__DIR__ . '/Fixture/SkipWithLink.php', []];
    }

    protected function getRule(): Rule
    {
        return new AnnotateRegexClassConstWithRegexLinkRule();
    }
}
