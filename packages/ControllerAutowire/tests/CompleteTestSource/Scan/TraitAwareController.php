<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests\CompleteTestSource\Scan;

use Symplify\ControllerAutowire\Controller\ControllerTrait;

final class TraitAwareController
{
    use ControllerTrait;

    public function someAction()
    {
    }
}
