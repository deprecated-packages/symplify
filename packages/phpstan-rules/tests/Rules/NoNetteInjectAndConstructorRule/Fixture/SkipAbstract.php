<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteInjectAndConstructorRule\Fixture;

abstract class SkipAbstract
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
