<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoReferenceRule\Source\AbstractSomeParentClassWithReference;

final class SkipParentMethodWithReference extends AbstractSomeParentClassWithReference
{
    public function someMethod(&$useIt)
    {
    }
}
