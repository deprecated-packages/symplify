<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\SeeAnnotationToTestRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\SeeAnnotationToTestRule;
use Symplify\PHPStanRules\Tests\Rules\SeeAnnotationToTestRule\Fixture\RuleWithoutSee;
use Symplify\PHPStanRules\Tests\Rules\SeeAnnotationToTestRule\Fixture\RuleWithSeeRandom;

/**
 * @extends AbstractServiceAwareRuleTestCase<SeeAnnotationToTestRule>
 */
final class SeeAnnotationToTestRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(SeeAnnotationToTestRule::ERROR_MESSAGE, RuleWithoutSee::class);
        yield [__DIR__ . '/Fixture/RuleWithoutSee.php', [[$errorMessage, 12]]];

        $errorMessage = sprintf(SeeAnnotationToTestRule::ERROR_MESSAGE, RuleWithSeeRandom::class);
        yield [__DIR__ . '/Fixture/RuleWithSeeRandom.php', [[$errorMessage, 15]]];

        yield [__DIR__ . '/Fixture/SkipDeprecatedRuleWithoutSee.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(SeeAnnotationToTestRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
