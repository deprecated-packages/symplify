<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoSetterOnServiceRule\Fixture\Contract;

interface SetValueInterface
{
    public function setValue($key);
}
