<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\PHPStanExtensions\Tests\TypeExtension\MethodCall\ContainerGetReturnTypeExtension\Source\ExternalService;
use function PHPStan\Testing\assertType;

class SomeClass
{
    public function run(ContainerInterface $container): void
    {
        $services = $container->get(ExternalService::class);
        assertType(ExternalService::class, $services);
    }
}
