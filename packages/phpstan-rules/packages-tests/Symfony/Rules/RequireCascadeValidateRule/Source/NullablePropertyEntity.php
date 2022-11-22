<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\RequireCascadeValidateRule\Source;

final class NullablePropertyEntity
{
    /**
     * @var AnotherPropertyObject|null
     */
    public $nullableProperty;
}
