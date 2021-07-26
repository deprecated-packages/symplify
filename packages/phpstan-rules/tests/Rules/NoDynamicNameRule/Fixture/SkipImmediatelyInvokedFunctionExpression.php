<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

final class SkipImmediatelyInvokedFunctionExpression
{
    /**
     * method copied from phpstan-src
     */
    public function load(string $key, string $variableKey)
    {
        return (function (string $key, string $variableKey) {
            [,, $filePath] = $this->getFilePaths($key);
            if (!is_file($filePath)) {
                return null;
            }

            $cacheItem = require $filePath;
            if (!$cacheItem instanceof CacheItem) {
                return null;
            }
            if (!$cacheItem->isVariableKeyValid($variableKey)) {
                return null;
            }

            return $cacheItem->getData();
        })($key, $variableKey);
    }
}
