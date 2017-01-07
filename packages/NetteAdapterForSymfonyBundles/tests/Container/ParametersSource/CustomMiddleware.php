<?php

declare(strict_types=1);

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\Container\ParametersSource;

use League\Tactician\Middleware;

final class CustomMiddleware implements Middleware
{
    /**
     * @param object $command
     * @param callable $next
     * @return object
     */
    public function execute($command, callable $next)
    {
        return $command;
    }
}
