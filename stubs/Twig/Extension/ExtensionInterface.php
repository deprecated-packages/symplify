<?php

declare(strict_types=1);

namespace Twig\Extension;

if (interface_exists(\Twig\Extension\ExtensionInterface::class)) {
    return;
}

interface ExtensionInterface
{
}
