<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\NoParentDuplicatedTraitUseRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\NoParentDuplicatedTraitUseRule;
use Symplify\PHPStanRules\Tests\Rules\Complexity\NoParentDuplicatedTraitUseRule\Source\SomeTrait;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoParentDuplicatedTraitUseRule>
 */
final class NoParentDuplicatedTraitUseRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipParentNoTrait.php', []];

        $errorMessage = sprintf(NoParentDuplicatedTraitUseRule::ERROR_MESSAGE, SomeTrait::class);
        yield [__DIR__ . '/Fixture/DuplicatedParentTrait.php', [[$errorMessage, 12]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoParentDuplicatedTraitUseRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
