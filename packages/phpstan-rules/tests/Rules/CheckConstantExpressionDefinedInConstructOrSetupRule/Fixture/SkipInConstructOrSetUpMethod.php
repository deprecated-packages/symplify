<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class SkipInConstructOrSetUpMethod
{
    private const A = 'a';
    private $a;

    public function setUp()
    {
        $this->a = self::A;
    }

    public function __construct()
    {
        $this->a = self::A;
    }
}
