<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ExcessiveParameterListRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ExcessiveParameterListRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ExcessiveParameterListRuleTest extends AbstractServiceAwareRuleTestCase
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
        $message = sprintf(ExcessiveParameterListRule::ERROR_MESSAGE, 'run', 10, 5);
        yield [__DIR__ . '/Fixture/TooManyParameters.php', [[$message, 9]]];
    }

    protected function getRule(): Rule
    {
        return new ExcessiveParameterListRule(5);
    }
}
