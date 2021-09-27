<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipDataProvider extends TestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test()
    {
    }

    public function provideData()
    {
        yield [__DIR__ . '/example.latte'];
    }
}
