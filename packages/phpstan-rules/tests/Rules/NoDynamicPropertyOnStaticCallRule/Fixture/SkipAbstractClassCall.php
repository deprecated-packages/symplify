<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\Source\AbstractSomeClass;

final class SkipAbstractClassCall
{
    public function run($object)
    {
        if (is_a($object, AbstractSomeClass::class, true)) {
            $content = $object::callMe();
        }
    }
}
