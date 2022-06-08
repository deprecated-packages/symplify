<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source;

use Symplify\PHPStanRules\Nette\Tests\Rules\NoInjectOnFinalRule\Source\SomeType;

abstract class AbstractParentClassWithMethodInject extends AbstractUnrelatedClass
{
    protected $someType;

    public function inject(SomeType $someType)
    {
        $this->someType = $someType;
    }
}
