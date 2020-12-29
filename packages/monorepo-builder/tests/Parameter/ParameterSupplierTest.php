<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Utils;

use Iterator;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Parameter\ParameterSupplier;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class ParameterSupplierTest extends AbstractKernelTestCase
{
    /**
     * @var ParameterSupplier
     */
    private $parameterSupplier;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->parameterSupplier = $this->getService(ParameterSupplier::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function testPackageDirectoriesAreComplete(array $configBefore, $configAfter): void
    {
        $this->assertEquals(
            $configAfter,
            $this->parameterSupplier->fillPackageDirectoriesWithDefaultData($configBefore)
        );
    }

    public function provideData(): Iterator
    {
        $completeConfig = [
            'symplify/monorepo-builder' => [
                'organization' => 'symplify',
            ],
            'symplify/package-builder' => [
                'organization' => 'symplify',
            ],
            'symplify/package-for-migrify' => [
                'organization' => 'migrify',
            ],
        ];
        yield [$completeConfig, $completeConfig];
    }

    // public function testFillPackageDirectories(): void
    // {
    //     $config = [
    //         'symplify/monorepo-builder' => [
    //             'foo' => 'bar',
    //         ],
    //         'symplify/package-builder' => [
    //             'organization' => 'symplify',
    //         ],
    //         'symplify/package-for-migrify' => [],
    //     ];
    //     $this->assertEquals(
    //         $config,
    //         $this->parameterSupplier->fillPackageDirectoriesWithDefaultData($config)
    //     );
    // }
}
