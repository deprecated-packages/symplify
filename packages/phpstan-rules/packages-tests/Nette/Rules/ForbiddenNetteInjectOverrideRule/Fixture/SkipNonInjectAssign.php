<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\ForbiddenNetteInjectOverrideRule\Fixture;

use Symplify\PHPStanRules\Tests\Nette\Rules\ForbiddenNetteInjectOverrideRule\Source\AbstractUnrelatedClass;
use Symplify\PHPStanRules\Tests\Nette\Rules\ForbiddenNetteInjectOverrideRule\Source\AnotherType;
use Symplify\PHPStanRules\Tests\Nette\Rules\NoInjectOnFinalRule\Source\SomeType;

final class SkipNonInjectAssign extends AbstractUnrelatedClass
{
    /**
     * @var SomeType
     */
    public $someType;

    public function __construct(AnotherType $anotherType)
    {
        $this->someType = $anotherType;
    }
}
