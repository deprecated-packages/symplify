<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoClassWithStaticMethodWithoutStaticNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoClassWithStaticMethodWithoutStaticNameRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoClassWithStaticMethodWithoutStaticNameRule>
 */
final class NoClassWithStaticMethodWithoutStaticNameRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = NoClassWithStaticMethodWithoutStaticNameRule::ERROR_MESSAGE;
        yield [__DIR__ . '/Fixture/ClassWithMethod.php', [[$errorMessage, 7]]];

        yield [__DIR__ . '/Fixture/SkipEventSubscriber.php', []];
        yield [__DIR__ . '/Fixture/SkipEventSubscriberWithAnotherStaticMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipValueObjectFactory.php', []];
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
        return self::getContainer()->getByType(NoClassWithStaticMethodWithoutStaticNameRule::class);
    }
}
