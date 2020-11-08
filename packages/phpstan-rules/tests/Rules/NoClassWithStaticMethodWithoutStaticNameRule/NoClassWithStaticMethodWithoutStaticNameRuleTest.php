<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoClassWithStaticMethodWithoutStaticNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoClassWithStaticMethodWithoutStaticNameRule;
use Symplify\PHPStanRules\Tests\Rules\NoClassWithStaticMethodWithoutStaticNameRule\Fixture\ClassWithMethod;

final class NoClassWithStaticMethodWithoutStaticNameRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(NoClassWithStaticMethodWithoutStaticNameRule::ERROR_MESSAGE, ClassWithMethod::class);
        yield [__DIR__ . '/Fixture/ClassWithMethod.php', [[$errorMessage, 7]]];

        yield [__DIR__ . '/Fixture/SkipEventSubscriber.php', []];
        yield [__DIR__ . '/Fixture/EventSubscriberWithAnotherStaticMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipValueObjectFactory.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoClassWithStaticMethodWithoutStaticNameRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
