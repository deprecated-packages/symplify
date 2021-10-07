<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Source;

final class SomeObjectWithMagicGet extends AbstractMagicGet
{
    public $assignedProperty;

    public function getId()
    {
        return 1000;
    }

    public function setId($value)
    {
    }

    private function getPrivateMethod()
    {
    }
}
