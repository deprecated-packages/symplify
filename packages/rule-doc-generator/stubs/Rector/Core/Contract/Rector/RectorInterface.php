<?php

declare(strict_types=1);

namespace Rector\Core\Contract\Rector;

if (interface_exists(RectorInterface::class)) {
    return;
}

interface RectorInterface
{
}
