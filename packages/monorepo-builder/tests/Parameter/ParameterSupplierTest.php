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

    public function testPackageDirectoriesAreComplete(): void
    {
        $config = [
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
        $this->assertEquals(
            $config,
            $this->parameterSupplier->fillPackageDirectoriesWithDefaultData($config)
        );
    }

    public function testFillPackageDirectories(): void
    {
        $config = [
            'symplify/monorepo-builder' => [
                'foo' => 'bar',
            ],
            'symplify/package-builder' => [
                'organization' => 'symplify',
            ],
            'symplify/package-for-migrify' => [],
        ];
        $this->assertEquals(
            $config,
            $this->parameterSupplier->fillPackageDirectoriesWithDefaultData($config)
        );
    }

    // /**
    //  * @dataProvider provideDataAlias()
    //  */
    // public function testAlias(string $currentVersion, string $expectedVersion): void
    // {
    //     $this->assertSame($expectedVersion, $this->parameterSupplier->getNextAliasFormat($currentVersion));
    // }

    // public function provideDataAlias(): Iterator
    // {
    //     yield ['v4.0.0', '4.1-dev'];
    //     yield ['4.0.0', '4.1-dev'];
    //     yield ['4.5.0', '4.6-dev'];
    //     yield ['v8.0-beta', '8.0-dev'];
    // }

    // /**
    //  * @dataProvider provideDataForRequiredNextVersion()
    //  */
    // public function testRequiredNextVersion(string $currentVersion, string $expectedVersion): void
    // {
    //     $this->assertSame($expectedVersion, $this->parameterSupplier->getRequiredNextFormat($currentVersion));
    // }

    // public function provideDataForRequiredNextVersion(): Iterator
    // {
    //     yield ['v4.0.0', '^4.1'];
    //     yield ['4.0.0', '^4.1'];
    //     yield ['8.0-beta', '^8.0'];
    // }

    // /**
    //  * @dataProvider provideDataForRequiredVersion()
    //  */
    // public function testRequiredVersion(string $currentVersion, string $expectedVersion): void
    // {
    //     $this->assertSame($expectedVersion, $this->parameterSupplier->getRequiredFormat($currentVersion));
    // }

    // public function provideDataForRequiredVersion(): Iterator
    // {
    //     yield ['v4.0.0', '^4.0'];
    //     yield ['4.0.0', '^4.0'];
    // }
}
