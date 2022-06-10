<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoInjectOnFinalRule\Fixture;

use Nette\DI\Attributes\Inject;
use Symplify\PHPStanRules\Tests\Nette\Rules\NoInjectOnFinalRule\Source\SomeType;

abstract class SkipAbstractClass
{
    /**
     * @inject
     * @var SomeType
     */
    public $someProperty;

    /**
     * @var SomeType
     */
    #[Inject]
    public $someAttributeProperty;
}
