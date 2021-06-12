<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Nette\ForbiddenNetteInjectOverrideRule\Fixture;

final class SkipCurrentMethodInject
{
    /**
     * @var SomeType
     */
    public $someType;

    public function injectThis(AnotherType $anotherType)
    {
        $this->someType = $anotherType;
    }
}
