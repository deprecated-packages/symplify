<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\SameNamedParamFamilyRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\SameNamedParamFamilyRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<SameNamedParamFamilyRule>
 */
final class SameNamedParamFamilyRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipSomeClassWithoutParents.php', []];
        yield [__DIR__ . '/Fixture/SkipWithCompatibleParent.php', []];
        yield [__DIR__ . '/Fixture/SkipContainerBuilderMissmatch.php', []];

        yield [__DIR__ . '/Fixture/SkipParentExtraNullableParam.php', []];

        $errorMessage = sprintf(SameNamedParamFamilyRule::ERROR_MESSAGE, '"$copy" should be "$original"');
        yield [__DIR__ . '/Fixture/ClassWithDifferentParent.php', [[$errorMessage, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(SameNamedParamFamilyRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
