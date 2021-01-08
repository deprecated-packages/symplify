<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoSetterOnServiceRule\Fixture\Entity;

final class SkipSomeEntity
{
    public $name;

    public function setName($name)
    {
        $this->name = $name;
    }
}
