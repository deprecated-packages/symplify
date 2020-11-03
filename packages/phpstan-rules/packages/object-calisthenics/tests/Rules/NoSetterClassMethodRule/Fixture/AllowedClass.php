<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoSetterClassMethodRule\Fixture;

final class AllowedClass
{
    public function setName()
    {
    }
}
