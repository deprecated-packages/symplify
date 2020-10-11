<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenProtectedPropertyRule\Fixture;

final class HasProtectedPropertyAndConstant
{
    public $a;
    private $b;
    protected $c;

    public static $d;
    private static $e;
    protected static $f;

    public const G = 'G';
    private const H = 'H';
    protected const I = 'I';
}