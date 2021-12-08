<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoInjectOnFinalRule\Fixture;

use Nette\DI\Attributes\Inject;
use Symplify\PHPStanRules\Nette\Tests\Rules\NoInjectOnFinalRule\Source\SomeType;

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
