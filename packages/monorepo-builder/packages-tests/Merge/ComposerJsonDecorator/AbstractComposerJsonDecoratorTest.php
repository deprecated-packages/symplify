<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Merge\ComposerJsonDecorator;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\EasyTesting\FixtureSplitter\TrioFixtureSplitter;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class AbstractComposerJsonDecoratorTest extends AbstractKernelTestCase
{
    /**
     * @var ComposerJsonFactory
     */
    protected $composerJsonFactory;

    /**
     * @var TrioFixtureSplitter
     */
    protected $trioFixtureSplitter;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);
        $this->composerJsonFactory = $this->getService(ComposerJsonFactory::class);

        $this->trioFixtureSplitter = new TrioFixtureSplitter();
    }

    protected function createComposerJson(array | SmartFileInfo | string $source): ComposerJson
    {
        if (is_string($source) && ! file_exists($source)) {
            return $this->composerJsonFactory->createFromString($source);
        }

        if (is_array($source)) {
            return $this->composerJsonFactory->createFromArray($source);
        }

        if ($source instanceof SmartFileInfo) {
            return $this->composerJsonFactory->createFromFileInfo($source);
        }

        return $this->composerJsonFactory->createFromFilePath($source);
    }

    protected function assertComposerJsonEquals(
        string | ComposerJson $firstComposerJson,
        ComposerJson $secondComposerJson
    ): void {
        if (is_string($firstComposerJson)) {
            $firstComposerJson = $this->createComposerJson($firstComposerJson);
        }

        $this->assertSame($firstComposerJson->getAutoload(), $secondComposerJson->getAutoload());
        $this->assertSame($firstComposerJson->getAutoloadDev(), $secondComposerJson->getAutoloadDev());

        $this->assertSame($firstComposerJson->getRequire(), $secondComposerJson->getRequire());
        $this->assertSame($firstComposerJson->getRequireDev(), $secondComposerJson->getRequireDev());
        $this->assertSame($firstComposerJson->getRepositories(), $secondComposerJson->getRepositories());

        $this->assertSame($firstComposerJson->getReplace(), $secondComposerJson->getReplace());
        $this->assertSame($firstComposerJson->getExtra(), $secondComposerJson->getExtra());
        $this->assertSame($firstComposerJson->getConfig(), $secondComposerJson->getConfig());

        $this->assertSame($firstComposerJson->getName(), $secondComposerJson->getName());
        $this->assertSame($firstComposerJson->getLicense(), $secondComposerJson->getLicense());
        $this->assertSame($firstComposerJson->getDescription(), $secondComposerJson->getDescription());
        $this->assertSame($firstComposerJson->getAuthors(), $secondComposerJson->getAuthors());
    }
}
