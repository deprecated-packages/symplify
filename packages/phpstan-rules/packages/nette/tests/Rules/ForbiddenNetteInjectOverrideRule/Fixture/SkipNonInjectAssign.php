<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Fixture;

use Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source\AbstractUnrelatedClass;
use Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source\AnotherType;
use Symplify\PHPStanRules\Nette\Tests\Rules\NoInjectOnFinalRule\Source\SomeType;

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
