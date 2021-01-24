<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\SingleNetteInjectMethodRule\Fixture;

final class SkipAnotherNamedMethod
{
    /**
     * @var string
     */
    private $name;

    public function __construct()
    {
        $this->name = 'yeah';
    }

    public function run()
    {
    }

    public function go()
    {
    }
}
