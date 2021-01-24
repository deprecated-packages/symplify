<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ValidNetteInjectRule\Fixture;

final class InvalidInject
{
    /**
     * @injectAth
     * @var SomeType
     */
    public $netteType;
}
