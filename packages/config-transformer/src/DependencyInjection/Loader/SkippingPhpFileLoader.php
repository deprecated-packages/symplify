<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\DependencyInjection\Loader;

use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * This is needed to avoid loading the PHP file.
 */
final class SkippingPhpFileLoader extends PhpFileLoader
{
    public function load(mixed $resource, string $type = null): string
    {
        return '';
    }
}
