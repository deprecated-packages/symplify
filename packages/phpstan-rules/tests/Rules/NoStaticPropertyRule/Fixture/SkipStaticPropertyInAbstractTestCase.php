<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoStaticPropertyRule\Fixture;

use PHPUnit\Framework\TestCase;

abstract class SkipStaticPropertyInAbstractTestCase extends TestCase
{
    static public $someService;

    protected function setUp(): void
    {
        self::$someService = 1000;
    }
}
