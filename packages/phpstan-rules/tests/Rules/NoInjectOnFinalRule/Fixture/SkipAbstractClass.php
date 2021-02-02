<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoInjectOnFinalRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoInjectOnFinalRule\Source\SomeType;

abstract class SkipAbstractClass
{
    /**
     * @inject
     * @var SomeType
     */
    public $someProperty;
}
