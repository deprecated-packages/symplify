<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNetteInjectOverrideRule\Source;

use Nette\DI\Attributes\Inject;

abstract class AbstractInjectParentClass
{
    /**
     * @inject
     * @var SomeType
     */
    public $someType;

    #[Inject]
    public SomeType $someAttributeType;
}
