<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Tests\ComposerJsonFactory;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\Tests\HttpKernel\ComposerJsonManipulatorKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ComposerJsonFactoryTest extends AbstractKernelTestCase
{
    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    protected function setUp(): void
    {
        $this->bootKernel(ComposerJsonManipulatorKernel::class);

        $this->composerJsonFactory = self::$container->get(ComposerJsonFactory::class);
    }

    public function test(): void
    {
        $composerJsonFilePath = __DIR__ . '/Source/some_composer.json';
        $composerJson = $this->composerJsonFactory->createFromFilePath($composerJsonFilePath);

        $fileInfo = $composerJson->getFileInfo();
        $this->assertInstanceOf(SmartFileInfo::class, $fileInfo);

        /** @var SmartFileInfo $fileInfo */
        $this->assertSame($composerJsonFilePath, $fileInfo->getRealPath());

        $this->assertCount(2, $composerJson->getAllClassmaps());

        $this->assertSame(['directory', 'src'], $composerJson->getPsr4AndClassmapDirectories());
    }
}
