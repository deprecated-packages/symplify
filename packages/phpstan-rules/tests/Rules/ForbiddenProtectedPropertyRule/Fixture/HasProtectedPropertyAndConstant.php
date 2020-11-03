<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenProtectedPropertyRule\Fixture;

final class HasProtectedPropertyAndConstant
{
    public $a;
    private $b;
    protected $c;

    public static $d;
    private static $e;
    protected static $f;
}
