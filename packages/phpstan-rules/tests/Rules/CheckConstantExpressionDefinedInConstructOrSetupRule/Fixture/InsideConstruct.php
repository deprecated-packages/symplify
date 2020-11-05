<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class InsideConstruct
{
    private const A = 'a';

    public function __construct()
    {
        $this->a = self::A;
    }
}
