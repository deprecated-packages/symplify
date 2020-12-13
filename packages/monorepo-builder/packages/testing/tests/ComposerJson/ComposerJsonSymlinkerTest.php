<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\Tests\ComposerJson;

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

    public function testItCanAppendPathRepository(): void
    {
        $mainComposerJson = new SmartFileInfo(__DIR__ . '/composer.json');
        $packageFileInfo = new SmartFileInfo(__DIR__ . '/packages/package-one/composer.json');

        $packageComposerJson = $this->jsonFileManager->loadFromFileInfo($packageFileInfo);

        $packageComposerJson = $this->composerJsonSymlinker->decoratePackageComposerJsonWithPackageSymlinks(
            $packageComposerJson,
            ['example/package-two'],
            $mainComposerJson
        );

        $this->assertSame([
            'name' => 'example/package-one',
            'repositories' => [
                [
                    'type' => 'path',
                    'url' => '../../packages/package-two',
                    'options' => [
                        'symlink' => false,
                    ],
                ],
                [
                    'type' => 'composer',
                    'url' => 'https://repo.packagist.com/acme-companies/',
                ],
                [
                    'packagist.org' => false,
                ],
            ],
        ], $packageComposerJson);
    }

    public function testItCanAddPathRepository(): void
    {
        $mainComposerJson = new SmartFileInfo(__DIR__ . '/composer.json');
        $packageFileInfo = new SmartFileInfo(__DIR__ . '/packages/package-two/composer.json');

        $packageComposerJson = $this->jsonFileManager->loadFromFileInfo($packageFileInfo);

        $packageComposerJson = $this->composerJsonSymlinker->decoratePackageComposerJsonWithPackageSymlinks(
            $packageComposerJson,
            ['example/package-one'],
            $mainComposerJson
        );

        $this->assertSame([
            'name' => 'example/package-two',
            'repositories' => [
                [
                    'type' => 'path',
                    'url' => '../../packages/package-one',
                    'options' => [
                        'symlink' => false,
                    ],
                ],
            ],
        ], $packageComposerJson);
    }
}
