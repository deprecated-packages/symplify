<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\Tests\ContainerBuilderFactory;

use PHPUnit\Framework\TestCase;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use Symplify\SymplifyKernel\ContainerBuilderFactory;

final class ContainerBuilderFactoryTest extends TestCase
{
    public function test()
    {
        $containerBuilderFactory = new ContainerBuilderFactory(new ParameterMergingLoaderFactory());

        $container = $containerBuilderFactory->create([__DIR__ . '/config/some_services.php'], [], []);

        $hasSmartFileSystemService = $container->has(SmartFileSystem::class);
        $this->assertTrue($hasSmartFileSystemService);
    }
}
