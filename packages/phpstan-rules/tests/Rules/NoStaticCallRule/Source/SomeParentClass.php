<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoStaticCallRule\Source;

abstract class SomeParentClass
{
    /**
     * @var int
     */
    private $value;

    public function __construct()
    {
        $this->value = 5;
    }
}
