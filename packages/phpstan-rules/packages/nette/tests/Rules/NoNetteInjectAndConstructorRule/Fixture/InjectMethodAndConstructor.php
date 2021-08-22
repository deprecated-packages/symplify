<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoNetteInjectAndConstructorRule\Fixture;

final class InjectMethodAndConstructor
{
    /**
     * @var string
     */
    private $name;

    public function __construct()
    {
        $this->name = 'hey';
    }

    public function injectAnything()
    {
        $this->name = 'hey';
    }
}
