<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\ValidNetteInjectRule\Fixture;

final class PrivateInjectMethod
{
    /**
     * @inject
     */
    private function autowire(SomeType $netteType)
    {
    }
}
