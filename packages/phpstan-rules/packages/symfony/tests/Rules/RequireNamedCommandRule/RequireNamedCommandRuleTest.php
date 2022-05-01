<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\RequireNamedCommandRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Symfony\Rules\RequireNamedCommandRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireNamedCommandRule>
 */
final class RequireNamedCommandRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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
        yield [__DIR__ . '/Fixture/SkipNamedCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractMissingNameCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipAttributeNamedCommand.php', []];

        yield [__DIR__ . '/Fixture/MissingNameCommand.php', [[RequireNamedCommandRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(RequireNamedCommandRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
