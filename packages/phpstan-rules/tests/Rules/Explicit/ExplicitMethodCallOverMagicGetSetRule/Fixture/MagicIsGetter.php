<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Source\SomeObjectWithMagicGet;

/**
 * This is used in Nette (https://github.com/nette/utils/blob/d51e35f52240a54018e6a03a8b5e4305560fc24a/src/SmartObject.php#L67) and Symfony magic getters, so we can safely assume to report it.
 */
final class MagicIsGetter
{
    public function run(SomeObjectWithMagicGet $someSmartObject)
    {
        return $someSmartObject->valid;
    }
}
