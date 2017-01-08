<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\DI\Helper\TypeAndCollectorTraitSource;

use Nette\DI\CompilerExtension;
use Symplify\Statie\DI\Helper\TypeAndCollectorTrait;

final class SmartExtension extends CompilerExtension
{
    use TypeAndCollectorTrait;
}
