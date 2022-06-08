<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source;

use Nette\DI\Attributes\Inject;
use Symplify\PHPStanRules\Nette\Tests\Rules\NoInjectOnFinalRule\Source\SomeType;

abstract class AbstractInjectParentClass
{
    /**
     * @inject
     * @var SomeType
     */
    public $someParentType;

    #[Inject]
    public SomeType $someAttributeType;
}
