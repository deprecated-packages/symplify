<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireSkipPrefixForRuleSkippedFixtureRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequireSkipPrefixForRuleSkippedFixtureRule;

final class RequireSkipPrefixForRuleSkippedFixtureRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipCorrectNaming.php', []];

        $errorMessage = sprintf(RequireSkipPrefixForRuleSkippedFixtureRule::ERROR_MESSAGE, 'CorrectNaming.php');
        yield [__DIR__ . '/Fixture/MissingPrefix.php', [[$errorMessage, 14]]];

        $errorMessage = sprintf(RequireSkipPrefixForRuleSkippedFixtureRule::INVERTED_ERROR_MESSAGE, 'SkipNaming.php');
        yield [__DIR__ . '/Fixture/ExtraPrefix.php', [[$errorMessage, 14]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireSkipPrefixForRuleSkippedFixtureRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
