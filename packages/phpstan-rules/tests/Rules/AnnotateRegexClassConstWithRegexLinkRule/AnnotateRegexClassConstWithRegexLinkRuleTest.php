<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\AnnotateRegexClassConstWithRegexLinkRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\AnnotateRegexClassConstWithRegexLinkRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<AnnotateRegexClassConstWithRegexLinkRule>
 */
final class AnnotateRegexClassConstWithRegexLinkRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [
            __DIR__ . '/Fixture/ClassConstMissingLink.php',
            [[AnnotateRegexClassConstWithRegexLinkRule::ERROR_MESSAGE, 12]],
        ];

        yield [__DIR__ . '/Fixture/SkipShort.php', []];
        yield [__DIR__ . '/Fixture/SkipWithLink.php', []];
        yield [__DIR__ . '/Fixture/SkipAlphabet.php', []];
        yield [__DIR__ . '/Fixture/SkipPlaceholder.php', []];
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
        return self::getContainer()->getByType(AnnotateRegexClassConstWithRegexLinkRule::class);
    }
}
