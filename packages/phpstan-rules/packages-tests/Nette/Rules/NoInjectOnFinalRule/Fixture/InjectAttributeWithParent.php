<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoInjectOnFinalRule\Fixture;

use Nette\DI\Attributes\Inject;
use Symplify\PHPStanRules\Tests\Nette\Rules\NoInjectOnFinalRule\Source\SomeType;

final class InjectAttributeWithParent extends SkipAbstractClass
{
    /**
     * @var SomeType
     */
    #[Inject]
    public $yetAnotherProperty;
}
