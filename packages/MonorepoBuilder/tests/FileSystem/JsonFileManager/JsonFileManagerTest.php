<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\FileSystem\JsonFileManager;

use Symfony\Component\Filesystem\Filesystem;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\Tests\AbstractContainerAwareTestCase;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class JsonFileManagerTest extends AbstractContainerAwareTestCase
{
    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var Filesystem
     */
    private $filesystem;

    protected function setUp(): void
    {
        $this->jsonFileManager = $this->container->get(JsonFileManager::class);
        $this->filesystem = $this->container->get(Filesystem::class);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove(__DIR__ . '/Source/second.json');
        $this->filesystem->remove(__DIR__ . '/Source/third.json');
    }

    public function testLoad(): void
    {
        $this->assertSame([
            'key' => 'value',
        ], $this->jsonFileManager->loadFromFilePath(__DIR__ . '/Source/first.json'));

        $this->assertSame([
            'key' => 'value',
        ], $this->jsonFileManager->loadFromFileInfo(new SmartFileInfo(__DIR__ . '/Source/first.json')));
    }

    public function testEncodeArrayToString(): void
    {
        $jsonContent = $this->jsonFileManager->encodeJsonToFileContent(['another_key' => 'another_value']);
        $this->assertStringEqualsFile(__DIR__ . '/Source/expected-second.json', $jsonContent);
    }

    public function testSaveWithInlinedSections(): void
    {
        $fileContent = $this->jsonFileManager->encodeJsonToFileContent([
            'inline_section' => [1, 2, 3],
            'normal_section' => [1, 2, 3],
        ], ['inline_section']);

        $this->assertStringEqualsFile(__DIR__ . '/Source/expected-inlined.json', $fileContent);
    }
}
