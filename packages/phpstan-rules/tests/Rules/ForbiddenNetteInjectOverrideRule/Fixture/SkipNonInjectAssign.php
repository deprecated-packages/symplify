<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNetteInjectOverrideRule\Fixture;

final class SkipNonInjectAssign
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
