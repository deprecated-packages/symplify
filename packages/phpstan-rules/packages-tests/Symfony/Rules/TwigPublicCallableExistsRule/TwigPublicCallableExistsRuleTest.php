<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\TwigPublicCallableExistsRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Symfony\Rules\TwigPublicCallableExistsRule;

/**
 * @extends RuleTestCase<TwigPublicCallableExistsRule>
 */
final class TwigPublicCallableExistsRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipTwigExtensionWithExistingCallable.php', []];

        $errorMessage = sprintf(TwigPublicCallableExistsRule::ERROR_MESSAGE, 'notHere');
        yield [__DIR__ . '/Fixture/TwigExtensionWithMissingCallable.php', [[$errorMessage, 15]]];
    }

    protected function getRule(): Rule
    {
        return new TwigPublicCallableExistsRule();
    }
}
