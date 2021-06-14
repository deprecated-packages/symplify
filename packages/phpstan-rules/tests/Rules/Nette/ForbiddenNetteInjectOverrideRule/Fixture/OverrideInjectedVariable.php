<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Nette\ForbiddenNetteInjectOverrideRule\Fixture;

final class OverrideInjectedVariable
{
    /**
     * @inject
     * @var SomeType
     */
    public $someType;

    public function __construct(AnotherType $anotherType)
    {
        $this->someType = $anotherType;
    }
}
