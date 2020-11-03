<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\Source;

abstract class AbstractSomeParentClassWithReference
{
    public function someMethod(&$useIt)
    {
    }
}
