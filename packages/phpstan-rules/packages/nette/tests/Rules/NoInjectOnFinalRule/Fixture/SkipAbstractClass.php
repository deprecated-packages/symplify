<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoInjectOnFinalRule\Fixture;

use Symplify\PHPStanRules\Nette\Tests\Rules\NoInjectOnFinalRule\Source\SomeType;

abstract class SkipAbstractClass
{
    /**
     * @inject
     * @var SomeType
     */
    public $someProperty;
}
