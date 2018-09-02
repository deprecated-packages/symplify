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
    public function testAlias(string $currentVersion, string $expectedDevVersion): void
    {
        $nextDevVersion = $this->utils->getNextVersionDevAliasForVersion($currentVersion);
        $this->assertSame($expectedDevVersion, $nextDevVersion->getVersionString());
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
    public function testRequiredNextVersion(string $currentVersion, string $expectedDevVersion): void
    {
        $nextDevVersion = $this->utils->getRequiredNextVersionForVersion($currentVersion);
        $this->assertSame($expectedDevVersion, $nextDevVersion->getVersionString());
    }

    public function provideDataForRequiredNextVersion(): Iterator
    {
        yield ['v4.0.0', '^4.1'];
        yield ['4.0.0', '^4.1'];
    }
}
