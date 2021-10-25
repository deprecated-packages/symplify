<?php

declare(strict_types=1);

namespace Symplify\Astral\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\Astral\DependencyInjection\Extension\AstralExtension;

final class AstralBundle extends Bundle
{
    protected function createContainerExtension(): AstralExtension
    {
        return new AstralExtension();
    }
}
