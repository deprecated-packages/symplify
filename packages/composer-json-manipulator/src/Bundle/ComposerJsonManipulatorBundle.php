<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ComposerJsonManipulatorExtension;

final class ComposerJsonManipulatorBundle extends Bundle
{
    protected function createContainerExtension(): ComposerJsonManipulatorExtension
    {
        return new ComposerJsonManipulatorExtension();
    }
}
