<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoSetterOnServiceRule\Fixture\Service;

final class SomeServiceWithSetter
{
    private $x;

    public function setX(stdClass $x)
    {
        $this->x = $x;
    }
}
