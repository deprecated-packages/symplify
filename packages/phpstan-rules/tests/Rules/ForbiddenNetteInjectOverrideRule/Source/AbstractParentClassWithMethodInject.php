<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source;

abstract class AbstractParentClassWithMethodInject
{
    protected $someType;

    public function inject(SomeType $someType)
    {
        $this->someType = $someType;
    }
}
