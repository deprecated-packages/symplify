<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoInjectOnFinalRule\Fixture;

abstract class SkipAbstractClass
{
    /**
     * @inject
     * @var SomeType
     */
    public $someProperty;
}
