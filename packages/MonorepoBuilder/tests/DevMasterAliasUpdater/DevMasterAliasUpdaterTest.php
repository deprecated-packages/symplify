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

    public function test(): void
    {
        $this->devMasterAliasUpdater->updateFileInfosWithAlias($this->getFileInfos(), '4.5-dev');

        $this->assertSame(
            file_get_contents(__DIR__ . '/Source/expected-first.json'),
            file_get_contents(__DIR__ . '/Source/first.json')
        );
    }

    protected function tearDown(): void
    {
        copy(__DIR__ . '/Source/backup-first.json', __DIR__ . '/Source/first.json');
    }

    /**
     * @return SplFileInfo[]
     */
    private function getFileInfos(): array
    {
        return [new SplFileInfo(__DIR__ . '/Source/first.json', 'Source/first.json', 'Source')];
    }
}
