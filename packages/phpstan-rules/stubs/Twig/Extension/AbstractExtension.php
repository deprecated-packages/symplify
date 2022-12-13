<?php

declare(strict_types=1);

namespace Twig\Extension;

if (class_exists(\Twig\Extension\AbstractExtension::class)) {
    return;
}

abstract class AbstractExtension implements ExtensionInterface
{
}
