<?php

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\Container\ParametersSource;

use League\Tactician\Middleware;

final class CustomMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function execute($command, callable $next)
    {
        return $command;
    }
}
