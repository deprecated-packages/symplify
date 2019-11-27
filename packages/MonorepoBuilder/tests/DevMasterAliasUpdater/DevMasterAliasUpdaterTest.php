<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\DevMasterAliasUpdater;

use Nette\Utils\FileSystem;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class DevMasterAliasUpdaterTest extends AbstractKernelTestCase
{
    /**
     * @var DevMasterAliasUpdater
     */
    private $devMasterAliasUpdater;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->devMasterAliasUpdater = self::$container->get(DevMasterAliasUpdater::class);
    }

    protected function tearDown(): void
    {
        FileSystem::copy(__DIR__ . '/Source/backup-first.json', __DIR__ . '/Source/first.json');
    }

    public function test(): void
    {
        $fileInfos = [new SmartFileInfo(__DIR__ . '/Source/first.json')];

        $this->devMasterAliasUpdater->updateFileInfosWithAlias($fileInfos, '4.5-dev');

        $this->assertFileEquals(__DIR__ . '/Source/expected-first.json', __DIR__ . '/Source/first.json');
    }
}
