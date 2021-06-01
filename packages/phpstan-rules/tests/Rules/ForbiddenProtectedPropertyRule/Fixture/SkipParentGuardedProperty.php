<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenProtectedPropertyRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenProtectedPropertyRule\Source\ParentClassWithProperty;

final class SkipParentGuardedProperty extends ParentClassWithProperty
{
    /**
     * @var string
     */
    protected $someName;
}
