<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredClassRule;

use DateTime as NativeDateTime;
use Iterator;
use Nette\Utils\DateTime;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Rules\PreferredClassRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<PreferredClassRule>
 */
final class PreferredClassRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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
        $errorMessage = sprintf(PreferredClassRule::ERROR_MESSAGE, NativeDateTime::class, DateTime::class);
        yield [__DIR__ . '/Fixture/ClassUsingOld.php', [[$errorMessage, 13]]];
        yield [__DIR__ . '/Fixture/ClassExtendingOld.php', [[$errorMessage, 9]]];
        yield [__DIR__ . '/Fixture/ClassMethodParameterUsingOld.php', [[$errorMessage, 11]]];
        yield [__DIR__ . '/Fixture/SomeStaticCall.php', [[$errorMessage, 13]]];

        yield [__DIR__ . '/Fixture/SkipPreferredExtendingTheOldOne.php', []];
        yield [__DIR__ . '/Fixture/SkipRequiredByContract.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(PreferredClassRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
