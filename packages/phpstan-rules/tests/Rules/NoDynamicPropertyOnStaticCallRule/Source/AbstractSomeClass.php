<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\Source;

abstract class AbstractSomeClass
{
    abstract static function callMe();
}
