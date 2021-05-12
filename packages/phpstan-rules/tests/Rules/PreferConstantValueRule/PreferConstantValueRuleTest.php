<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferConstantValueRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\PreferConstantValueRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<PreferConstantValueRule>
 */
final class PreferConstantValueRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNotFoundInConstantValue.php', []];
        yield [__DIR__ . '/Fixture/SkipUseDefinedConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipStringInConstant.php', []];
        yield [__DIR__ . '/Fixture/FoundInConstantValue.php', [
            [
                sprintf(
                    PreferConstantValueRule::ERROR_MESSAGE,
                    ComposerJsonSection::class,
                    'REQUIRE',
                    ComposerJsonSection::REQUIRE
                ),
                11,
            ],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(PreferConstantValueRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
