<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Source;

final class SomeObjectWithMagicSet extends AbstractMagicSet
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
