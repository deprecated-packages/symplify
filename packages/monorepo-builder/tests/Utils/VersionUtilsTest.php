<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Utils;

use Iterator;
use Symplify\MonorepoBuilder\Kernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Utils\VersionUtils;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class VersionUtilsTest extends AbstractKernelTestCase
{
    private VersionUtils $versionUtils;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);
        $this->versionUtils = $this->getService(VersionUtils::class);
    }

    /**
     * @dataProvider provideDataAlias()
     */
    public function testAlias(string $currentVersion, string $expectedVersion): void
    {
        $nextAliasVersion = $this->versionUtils->getNextAliasFormat($currentVersion);
        $this->assertSame($expectedVersion, $nextAliasVersion);
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
        $nextRequiredVersion = $this->versionUtils->getRequiredNextFormat($currentVersion);
        $this->assertSame($expectedVersion, $nextRequiredVersion);
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
        $requiredVersion = $this->versionUtils->getRequiredFormat($currentVersion);
        $this->assertSame($expectedVersion, $requiredVersion);
    }

    /**
     * @return \Iterator<string[]>
     */
    public function provideDataForRequiredVersion(): Iterator
    {
        yield ['v4.0.0', '^4.0'];
        yield ['4.0.0', '^4.0'];
    }
}
