<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayWithStringKeysRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\Contract\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\ForbiddenArrayWithStringKeysRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenArrayWithStringKeysRule>
 */
final class ForbiddenArrayWithStringKeysRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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
        yield [__DIR__ . '/Fixture/ArrayWithStrings.php', [[ForbiddenArrayWithStringKeysRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/SkipConfigurationFormat.php', []];
        yield [__DIR__ . '/Fixture/SkipConfigurationArrayDimFetch.php', []];
        yield [__DIR__ . '/Fixture/SkipJsonSerializable.php', []];
        yield [__DIR__ . '/Fixture/SkipArrayRequiredParentContract.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInTest.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInTestCase.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInGetDefinition.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInConstantDefinition.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInNew.php', []];
        yield [__DIR__ . '/Fixture/SkipDataInCall.php', []];
        yield [__DIR__ . '/Fixture/SkipNonConstantString.php', []];
        yield [__DIR__ . '/Fixture/SkipDefaultValueInConstructor.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenArrayWithStringKeysRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
