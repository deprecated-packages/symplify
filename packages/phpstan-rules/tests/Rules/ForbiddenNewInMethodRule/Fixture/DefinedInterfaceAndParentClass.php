<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNewInMethodRule\Fixture;

final class DefinedInterfaceAndParentClass extends AbstractFoo implements InterfaceFoo
{
    public function run(): Rule
    {
        new SomeRule();
    }
}
