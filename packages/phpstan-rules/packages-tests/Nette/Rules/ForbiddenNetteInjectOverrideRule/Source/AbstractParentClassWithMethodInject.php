<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\ForbiddenNetteInjectOverrideRule\Source;

use Symplify\PHPStanRules\Tests\Nette\Rules\NoInjectOnFinalRule\Source\SomeType;

abstract class AbstractParentClassWithMethodInject extends AbstractUnrelatedClass
{
    protected $someType;

    public function inject(SomeType $someType)
    {
        $this->someType = $someType;
    }
}
