<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Routing;

/**
 * @see \Symplify\ConfigTransformer\Tests\Routing\RoutingConfigDetectorTest
 */
final class RoutingConfigDetector
{
    public function isRoutingFilePath(string $filePath): bool
    {
        if (str_contains($filePath, DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR)) {
            return false;
        }

        // if the paths contains this keyword, we assume it contains routes
        if (str_contains($filePath, 'routing')) {
            return true;
        }

        return str_contains($filePath, 'routes');
    }
}
