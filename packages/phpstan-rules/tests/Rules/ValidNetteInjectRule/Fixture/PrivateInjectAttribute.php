<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ValidNetteInjectRule\Fixture;

use Nette\DI\Attributes\Inject;

final class PrivateInjectAttribute
{
    /**
     * @var SomeType
     */
    #[Inject]
    private $netteType;
}
