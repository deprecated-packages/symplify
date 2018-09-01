<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\DevMasterAliasUpdater;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;

final class DevMasterAliasUpdaterTest extends TestCase
{
    /**
     * @var DevMasterAliasUpdater
     */
    private $devMasterAliasUpdater;

    protected function setUp(): void
    {
        $this->devMasterAliasUpdater = new DevMasterAliasUpdater(new JsonFileManager());
    }

    protected function tearDown(): void
    {
        copy(__DIR__ . '/Source/backup-first.json', __DIR__ . '/Source/first.json');
    }

    public function test(): void
    {
        $fileInfos = [new SplFileInfo(__DIR__ . '/Source/first.json', 'Source/first.json', 'Source')];

        $this->devMasterAliasUpdater->updateFileInfosWithAlias($fileInfos, '4.5-dev');

        $this->assertFileEquals(__DIR__ . '/Source/expected-first.json', __DIR__ . '/Source/first.json');
    }
}
