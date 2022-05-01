<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ExclusiveNamespaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\ExclusiveNamespaceRule;

/**
 * @extends RuleTestCase<ExclusiveNamespaceRule>
 */
final class ExclusiveNamespaceRuleTest extends RuleTestCase
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

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(ExclusiveNamespaceRule::class);
    }
}
