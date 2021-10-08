<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Source;

abstract class AbstractMagicSet
{
    public function __set($name, $value)
    {
        // some magic to call method, e.g. https://github.com/nette/utils/blob/d51e35f52240a54018e6a03a8b5e4305560fc24a/src/SmartObject.php#L59
    }
}
