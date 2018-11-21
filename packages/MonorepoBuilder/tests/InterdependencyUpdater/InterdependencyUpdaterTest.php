<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\InterdependencyUpdater;

use Symfony\Component\Filesystem\Filesystem;
use Symplify\MonorepoBuilder\InterdependencyUpdater;
use Symplify\MonorepoBuilder\Tests\AbstractContainerAwareTestCase;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class InterdependencyUpdaterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var InterdependencyUpdater
     */
    private $interdependencyUpdater;

    /**
     * @var Filesystem
     */
    private $filesystem;

    protected function setUp(): void
    {
        $this->interdependencyUpdater = $this->container->get(InterdependencyUpdater::class);
        $this->filesystem = $this->container->get(Filesystem::class);
    }

    protected function tearDown(): void
    {
        $this->filesystem->copy(__DIR__ . '/Source/backup-first.json', __DIR__ . '/Source/first.json', true);
    }

    public function testVendor(): void
    {
        $this->interdependencyUpdater->updateFileInfosWithVendorAndVersion(
            [new SmartFileInfo(__DIR__ . '/Source/first.json')],
            'symplify',
            '^5.0'
        );

        $this->assertFileEquals(__DIR__ . '/Source/expected-first-vendor.json', __DIR__ . '/Source/first.json');
    }

    public function testPackages(): void
    {
        $this->interdependencyUpdater->updateFileInfosWithPackagesAndVersion(
            [new SmartFileInfo(__DIR__ . '/Source/first.json')],
            ['symplify/coding-standard'],
            '^6.0'
        );

        $this->assertFileEquals(__DIR__ . '/Source/expected-first-packages.json', __DIR__ . '/Source/first.json');
    }
}
