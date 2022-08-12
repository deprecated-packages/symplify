<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\Source\SomeObject;

final class SkipUsedAsFormTypeDefaultClass
{
    public function run()
    {
        $someObject = new SomeObject();
        $someObject->setName('Joe');

        $anotherObject = new SomeObject();
        $anotherObject->setName('Doe');
    }
}
