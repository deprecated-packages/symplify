<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoInjectOnFinalRule\Fixture;

use Nette\DI\Attributes\Inject;
use Symplify\PHPStanRules\Nette\Tests\Rules\NoInjectOnFinalRule\Source\SomeType;

final class InjectAttributeWithParent extends SkipAbstractClass
{
    /**
     * @var SomeType
     */
    #[Inject]
    public $yetAnotherProperty;
}
