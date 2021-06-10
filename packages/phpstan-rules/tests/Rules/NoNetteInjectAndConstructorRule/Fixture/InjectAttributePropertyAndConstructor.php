<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteInjectAndConstructorRule\Fixture;

use Nette\DI\Attributes\Inject;

final class InjectAttributePropertyAndConstructor
{
    #[Inject]
    public $name;

    public function __construct()
    {
        $this->name = 'hey';
    }
}
