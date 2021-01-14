<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\Tests\ComposerJson;

use Iterator;
use Nette\Utils\Json;
use Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Testing\ComposerJson\ComposerJsonSymlinker;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ComposerJsonSymlinkerTest extends AbstractKernelTestCase
{
    /**
     * @var ComposerJsonSymlinker
     */
    private $composerJsonSymlinker;

    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->jsonFileManager = $this->getService(JsonFileManager::class);
        $this->composerJsonSymlinker = $this->getService(ComposerJsonSymlinker::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function testItCanAppendPathRepository(
        string $packagePath,
        string $packageName,
        bool $symlink,
        string $expectedJsonFile
    ): void {
        $mainComposerJson = new SmartFileInfo(__DIR__ . '/composer.json');
        $packageFileInfo = new SmartFileInfo($packagePath);

        $packageComposerJson = $this->jsonFileManager->loadFromFileInfo($packageFileInfo);

        $packageComposerJson = $this->composerJsonSymlinker->decoratePackageComposerJsonWithPackageSymlinks(
            $packageComposerJson,
            [$packageName],
            $mainComposerJson,
            $symlink
        );

        $jsonString = Json::encode($packageComposerJson, Json::PRETTY);
        $this->assertJsonStringEqualsJsonFile($expectedJsonFile, $jsonString);
    }

    public function provideData(): Iterator
    {
        yield [
            __DIR__ . '/packages/package-one/composer.json',
            'example/package-two',
            false,
            __DIR__ . '/Fixture/expected_path_repository.json',
        ];

        yield [
            __DIR__ . '/packages/package-two/composer.json',
            'example/package-one',
            false,
            __DIR__ . '/Fixture/expected_repository.json',
        ];

        yield [
            __DIR__ . '/packages/package-two/composer.json',
            'example/package-one',
            true,
            __DIR__ . '/Fixture/expected_symlink_true.json',
        ];

        yield [
            __DIR__ . '/packages/package-three/composer.json',
            'example/package-one',
            false,
            __DIR__ . '/Fixture/expected_reuse_existing_repository.json',
        ];
    }
}
