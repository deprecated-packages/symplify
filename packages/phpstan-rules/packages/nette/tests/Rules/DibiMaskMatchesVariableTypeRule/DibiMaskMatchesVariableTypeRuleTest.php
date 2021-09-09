<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\DibiMaskMatchesVariableTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Nette\Rules\DibiMaskMatchesVariableTypeRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<DibiMaskMatchesVariableTypeRule>
 */
final class DibiMaskMatchesVariableTypeRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(
            DibiMaskMatchesVariableTypeRule::ERROR_MESSAGE,
            '%v',
            ConstantStringType::class,
            ArrayType::class
        );
        yield [__DIR__ . '/Fixture/InvalidType.php', [[$errorMessage, 13]]];

        yield [__DIR__ . '/Fixture/SkipMatchingType.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            DibiMaskMatchesVariableTypeRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
