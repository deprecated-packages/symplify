<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\ValidNetteInjectRule\Fixture;

final class SkipCorrectInject
{
    /**
     * @inject
     * @var SomeType
     */
    public $netteType;
}
