<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Source;

final class NormalObject
{
    private $id;

    public function getId()
    {
        return $this->id;
    }

    public $normalProperty;
}
