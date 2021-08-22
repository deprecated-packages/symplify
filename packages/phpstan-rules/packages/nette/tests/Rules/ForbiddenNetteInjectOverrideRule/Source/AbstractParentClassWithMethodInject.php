<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Nette\ForbiddenNetteInjectOverrideRule\Source;

abstract class AbstractParentClassWithMethodInject
{
    protected $someType;

    public function inject(SomeType $someType)
    {
        $this->someType = $someType;
    }
}
