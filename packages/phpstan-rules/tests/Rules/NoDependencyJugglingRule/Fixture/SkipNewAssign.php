<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDependencyJugglingRule\Fixture;

use PHPStan\Type\ObjectType;

final class SkipNewAssign
{
    /**
     * @var ObjectType
     */
    private $objectType;

    public function __construct()
    {
        // probably just value object
        $this->objectType = new ObjectType('SomeService');
    }

    public function another(ObjectType $anotherObjectType)
    {
        return $anotherObjectType->isSuperTypeOf($this->objectType)->yes();
    }
}
