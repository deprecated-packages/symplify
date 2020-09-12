<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\NoNewOutsideFactoryRule;
use Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule\Source\SomeValueObject;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class NoNewOutsideFactoryRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(NoNewOutsideFactoryRule::ERROR_MESSAGE, SomeValueObject::class);
        yield [__DIR__ . '/Fixture/SomeNew.php', [[$errorMessage, 13]]];

        yield [__DIR__ . '/Fixture/SkipException.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoNewOutsideFactoryRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
