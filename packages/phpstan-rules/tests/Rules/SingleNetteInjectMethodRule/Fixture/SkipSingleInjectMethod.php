<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\SingleNetteInjectMethodRule\Fixture;

final class SkipSingleInjectMethod
{
    private $type;

    public function injectOne($type)
    {
        $this->type = $type;
    }
}

