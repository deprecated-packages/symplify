<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNetteInjectPropertyAssignRule\Source;

abstract class AbstractInjectParentClass
{
    /**
     * @inject
     * @var SomeType
     */
    public $someType;
}
