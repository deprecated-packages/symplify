<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Tests\ComposerJsonFactory;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\Tests\HttpKernel\ComposerJsonManipulatorKernel;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ComposerJsonFactoryTest extends AbstractKernelTestCase
{
    /**
     * @var ComposerJson
     */
    private $composerJson;

    protected function setUp(): void
    {
        $this->bootKernel(ComposerJsonManipulatorKernel::class);

        $composerJsonFactory = self::$container->get(ComposerJsonFactory::class);
        $composerJsonFilePath = __DIR__ . '/Source/some_composer.json';

        $this->composerJson = $composerJsonFactory->createFromFilePath($composerJsonFilePath);
    }

    public function test(): void
    {
        $fileInfo = $this->composerJson->getFileInfo();
        $this->assertInstanceOf(SmartFileInfo::class, $fileInfo);

        /** @var SmartFileInfo $fileInfo */
        $this->assertCount(2, $this->composerJson->getAllClassmaps());

        $this->assertSame(['directory', 'src'], $this->composerJson->getPsr4AndClassmapDirectories());

        $this->assertSame([
            'symplify/autodiscovery' => '^8.3.45',
        ], $this->composerJson->getReplace());

        $this->assertSame('project', $this->composerJson->getType());
    }
}
