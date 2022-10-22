<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\RequireCascadeValidateRule\Source;

final class SomeEntity
{
    /**
     * @var AnotherPropertyObject
     */
    public $anotherPropertyObject;

    public AnotherPropertyObject $typedProperty;
}
