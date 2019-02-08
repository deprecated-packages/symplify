<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Utils;

use Iterator;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Utils\Utils;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class UtilsTest extends AbstractKernelTestCase
{
    /**
     * @var Utils
     */
    private $utils;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->utils = self::$container->get(Utils::class);
    }

    /**
     * @dataProvider provideDataAlias()
     */
    public function testAlias(string $currentVersion, string $expectedVersion): void
    {
        $this->assertSame($expectedVersion, $this->utils->getNextAliasFormat($currentVersion));
    }

    public function provideDataAlias(): Iterator
    {
        yield ['v4.0.0', '4.1-dev'];
        yield ['4.0.0', '4.1-dev'];
        yield ['4.5.0', '4.6-dev'];
    }

    /**
     * @dataProvider provideDataForRequiredNextVersion()
     */
    public function testRequiredNextVersion(string $currentVersion, string $expectedVersion): void
    {
        $this->assertSame($expectedVersion, $this->utils->getRequiredNextFormat($currentVersion));
    }

    public function provideDataForRequiredNextVersion(): Iterator
    {
        yield ['v4.0.0', '^4.1'];
        yield ['4.0.0', '^4.1'];
    }

    /**
     * @dataProvider provideDataForRequiredVersion()
     */
    public function testRequiredVersion(string $currentVersion, string $expectedVersion): void
    {
        $this->assertSame($expectedVersion, $this->utils->getRequiredFormat($currentVersion));
    }

    public function provideDataForRequiredVersion(): Iterator
    {
        yield ['v4.0.0', '^4.0'];
        yield ['4.0.0', '^4.0'];
    }
}
