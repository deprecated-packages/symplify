<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

class InsideConstruct
{
    private const A = 'a';

    public function __construct()
    {
        $a = self::A;
    }
}
