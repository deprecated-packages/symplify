<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ExclusiveNamespaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ExclusiveNamespaceRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ExclusiveNamespaceRule>
 */
final class ExclusiveNamespaceRuleTest extends AbstractServiceAwareRuleTestCase
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
            ExclusiveNamespaceRule::ERROR_MESSAGE,
            'Symplify\PHPStanRules\Tests\Rules\ExclusiveNamespaceRule\Fixture\Presenter',
            'Presenter'
        );
        yield [__DIR__ . '/Fixture/Presenter/SomeRepository.php', [[$errorMessage, 7]]];

        yield [__DIR__ . '/Fixture/Presenter/Contract/SkipContract.php', []];
        yield [__DIR__ . '/Fixture/Presenter/Exception/SkipException.php', []];
        yield [__DIR__ . '/Fixture/Presenter/SkipSomeTest.php', []];
        yield [__DIR__ . '/Fixture/Presenter/SkipPresenter.php', []];
        yield [__DIR__ . '/Fixture/SkipNotMatched.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ExclusiveNamespaceRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
