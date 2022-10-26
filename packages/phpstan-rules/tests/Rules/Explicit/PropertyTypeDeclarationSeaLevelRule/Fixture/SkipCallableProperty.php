<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\PropertyTypeDeclarationSeaLevelRule\Fixture;

final class SkipCallableProperty
{
    /**
     * @var callable
     */
    public $someCallable;
}
