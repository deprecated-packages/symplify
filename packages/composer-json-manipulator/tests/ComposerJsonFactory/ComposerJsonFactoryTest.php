<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Tests\ComposerJsonFactory;

use PHPUnit\Framework\Constraint\JsonMatches;
use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\ComposerJsonManipulator\Tests\HttpKernel\ComposerJsonManipulatorKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ComposerJsonFactoryTest extends AbstractKernelTestCase
{
    protected function setUp(): void
    {
        $this->bootKernel(ComposerJsonManipulatorKernel::class);
    }

    public function test(): void
    {
        $composerJsonFactory = $this->getService(ComposerJsonFactory::class);
        $composerJson = $composerJsonFactory->createFromFilePath(__DIR__ . '/Source/some_composer.json');

        $fileInfo = $composerJson->getFileInfo();
        $this->assertInstanceOf(SmartFileInfo::class, $fileInfo);

        /** @var SmartFileInfo $fileInfo */
        $this->assertCount(2, $composerJson->getAllClassmaps());

        $this->assertSame(['directory', 'src'], $composerJson->getPsr4AndClassmapDirectories());

        $this->assertSame([
            'symplify/between' => '^8.3.45',
        ], $composerJson->getReplace());

        $this->assertSame('project', $composerJson->getType());
    }

    public function testReadAndWriteToJsonShouldBeEqual(): void
    {
        $file = __DIR__ . '/Source/full_composer.json';

        $composerJsonFactory = $this->getService(ComposerJsonFactory::class);
        $jsonFileManager = $this->getService(JsonFileManager::class);

        $composerJson = $composerJsonFactory->createFromFilePath($file);
        $actualJson = $jsonFileManager->encodeJsonToFileContent($composerJson->getJsonArray());

        $smartFileSystem = new SmartFileSystem();
        $expectedJson = $smartFileSystem->readFile($file);

        $this->assertThat($expectedJson, new JsonMatches($actualJson));
        $this->assertThat($actualJson, new JsonMatches($expectedJson));
    }
}
