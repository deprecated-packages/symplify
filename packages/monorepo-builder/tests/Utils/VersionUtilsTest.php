<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Utils;

use Iterator;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Utils\VersionUtils;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class VersionUtilsTest extends AbstractKernelTestCase
{
    /**
     * @var VersionUtils
     */
    private $versionUtils;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->versionUtils = self::$container->get(VersionUtils::class);
    }

    /**
     * @dataProvider provideDataAlias()
     */
    public function testAlias(string $currentVersion, string $expectedVersion): void
    {
        $this->assertSame($expectedVersion, $this->versionUtils->getNextAliasFormat($currentVersion));
    }

    public function provideDataAlias(): Iterator
    {
        yield ['v4.0.0', '4.1-dev'];
        yield ['4.0.0', '4.1-dev'];
        yield ['4.5.0', '4.6-dev'];
        yield ['v8.0-beta', '8.0-dev'];
    }

    /**
     * @dataProvider provideDataForRequiredNextVersion()
     */
    public function testRequiredNextVersion(string $currentVersion, string $expectedVersion): void
    {
        $this->assertSame($expectedVersion, $this->versionUtils->getRequiredNextFormat($currentVersion));
    }

    public function provideDataForRequiredNextVersion(): Iterator
    {
        yield ['v4.0.0', '^4.1'];
        yield ['4.0.0', '^4.1'];
        yield ['8.0-beta', '^8.0'];
    }

    /**
     * @dataProvider provideDataForRequiredVersion()
     */
    public function testRequiredVersion(string $currentVersion, string $expectedVersion): void
    {
        $this->assertSame($expectedVersion, $this->versionUtils->getRequiredFormat($currentVersion));
    }

    public function provideDataForRequiredVersion(): Iterator
    {
        yield ['v4.0.0', '^4.0'];
        yield ['4.0.0', '^4.0'];
    }
}
