<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\SeeAnnotationToTestRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\SeeAnnotationToTestRule;
use Symplify\CodingStandard\Tests\Rules\SeeAnnotationToTestRule\Fixture\RuleWithoutSee;
use Symplify\CodingStandard\Tests\Rules\SeeAnnotationToTestRule\Fixture\RuleWithSeeRandom;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class SeeAnnotationToTestRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(SeeAnnotationToTestRule::ERROR_MESSAGE, RuleWithoutSee::class);
        yield [__DIR__ . '/Fixture/RuleWithoutSee.php', [[$errorMessage, 10]]];

        $errorMessage = sprintf(SeeAnnotationToTestRule::ERROR_MESSAGE, RuleWithSeeRandom::class);
        yield [__DIR__ . '/Fixture/RuleWithSeeRandom.php', [[$errorMessage, 13]]];

        yield [__DIR__ . '/Fixture/DeprecatedRuleWithoutSee.php', []];
        yield [__DIR__ . '/Fixture/RuleWithSee.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(SeeAnnotationToTestRule::class, __DIR__ . '/config/see_rule_config.neon');
    }
}
