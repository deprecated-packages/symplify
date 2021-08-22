<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\ValidNetteInjectRule\Fixture;

final class PrivateInject
{
    /**
     * @inject
     * @var SomeType
     */
    private $netteType;
}
