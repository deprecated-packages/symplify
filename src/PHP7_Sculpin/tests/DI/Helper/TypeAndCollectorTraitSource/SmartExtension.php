<?php

namespace Symplify\PHP7_Sculpin\Tests\DI\Helper\TypeAndCollectorTraitSource;

use Nette\DI\CompilerExtension;
use Symplify\PHP7_Sculpin\DI\Helper\TypeAndCollectorTrait;

final class SmartExtension extends CompilerExtension
{
    use TypeAndCollectorTrait;
}
