<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Utils;

use Iterator;
use Symplify\MonorepoBuilder\Tests\AbstractContainerAwareTestCase;
use Symplify\MonorepoBuilder\Utils\Utils;

final class UtilsTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Utils
     */
    private $utils;

    protected function setUp(): void
    {
        $this->utils = $this->container->get(Utils::class);
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
