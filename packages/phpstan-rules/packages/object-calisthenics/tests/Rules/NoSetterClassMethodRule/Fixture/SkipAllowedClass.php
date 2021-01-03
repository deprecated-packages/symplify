<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoSetterClassMethodRule\Fixture;

final class SkipAllowedClass
{
    public function setName()
    {
    }
}
