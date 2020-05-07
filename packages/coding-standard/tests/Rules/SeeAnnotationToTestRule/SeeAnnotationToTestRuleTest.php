<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\SeeAnnotationToTestRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\SeeAnnotationToTestRule;
use Symplify\CodingStandard\Tests\Rules\SeeAnnotationToTestRule\Fixture\RuleWithoutSee;
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
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(SeeAnnotationToTestRule::class, __DIR__ . '/config/see_rule_config.neon');
    }
}
