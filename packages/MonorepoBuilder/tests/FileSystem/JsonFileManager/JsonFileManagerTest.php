<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\FileSystem\JsonFileManager;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\Tests\AbstractContainerAwareTestCase;

final class JsonFileManagerTest extends AbstractContainerAwareTestCase
{
    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    protected function setUp(): void
    {
        $this->jsonFileManager = $this->container->get(JsonFileManager::class);
    }

    protected function tearDown(): void
    {
        @unlink(__DIR__ . '/Source/second.json');
        @unlink(__DIR__ . '/Source/third.json');
    }

    public function testLoad(): void
    {
        $this->assertSame([
            'key' => 'value',
        ], $this->jsonFileManager->loadFromFilePath(__DIR__ . '/Source/first.json'));

        $this->assertSame([
            'key' => 'value',
        ], $this->jsonFileManager->loadFromFileInfo(new SplFileInfo(__DIR__ . '/Source/first.json', '...', '...')));
    }

    public function testSave(): void
    {
        $this->jsonFileManager->saveJsonWithFilePath(
            ['another_key' => 'another_value'],
            __DIR__ . '/Source/second.json'
        );
        $this->assertFileEquals(__DIR__ . '/Source/expected-second.json', __DIR__ . '/Source/second.json');

        $this->jsonFileManager->saveJsonWithFileInfo(
            ['yet_another_key' => 'yet_another_value'],
            new SplFileInfo(__DIR__ . '/Source/third.json', '', '')
        );
        $this->assertFileEquals(__DIR__ . '/Source/expected-third.json', __DIR__ . '/Source/third.json');
    }
}
