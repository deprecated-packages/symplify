<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\ValidNetteInjectRule\Fixture;

use Nette\DI\Attributes\Inject;

final class SkipCorrectInjectAttribute
{
    #[Inject]
    /**
     * @var SomeType
     */
    public $netteType;
}
