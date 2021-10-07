<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Fixture;

use Nette\SmartObject;

final class SkipLocalGetter
{
    use SmartObject;

    private $value;

    public function getValue()
    {
        return $this->value;
    }
}
