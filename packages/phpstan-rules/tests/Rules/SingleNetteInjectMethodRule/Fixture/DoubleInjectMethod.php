<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\SingleNetteInjectMethodRule\Fixture;

final class DoubleInjectMethod
{
    private $type;

    private $anotherType;

    public function injectOne($type)
    {
        $this->type = $type;
    }

    public function injectTwo($anotherType)
    {
        $this->anotherType = $anotherType;
    }
}

