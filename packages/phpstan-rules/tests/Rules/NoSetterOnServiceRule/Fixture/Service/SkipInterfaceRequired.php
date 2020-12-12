<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoSetterOnServiceRule\Fixture\Service;

use Symplify\PHPStanRules\Tests\Rules\NoSetterOnServiceRule\Fixture\Contract\SetValueInterface;

final class SkipInterfaceRequired implements SetValueInterface
{
    private $key;

    public function setValue($key)
    {
        $this->key = $key;
    }
}
