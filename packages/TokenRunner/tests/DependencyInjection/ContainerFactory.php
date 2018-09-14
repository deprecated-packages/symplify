<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\DependencyInjection;

use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $kernel = new TokenRunnerKernel(__DIR__ . '/../config/config_tests.yaml');
        $kernel->boot();

        return $kernel->getContainer();
    }
}
