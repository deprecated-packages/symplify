<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenProtectedPropertyRule\Fixture;

final class SkipHasNonProtectedPropertyAndConstant
{
    public $a;
    private $b;

    public static $c;
    private static $d;

    public const E = 'E';
    private const F = 'F';
}
