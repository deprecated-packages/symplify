<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ValidNetteInjectRule\Fixture;

final class PrivateInject
{
    /**
     * @inject
     * @var SomeType
     */
    private $netteType;
}
