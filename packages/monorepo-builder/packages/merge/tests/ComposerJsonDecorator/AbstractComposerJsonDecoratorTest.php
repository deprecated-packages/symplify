<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Symplify\MonorepoBuilder\ComposerJsonObject\ComposerJsonFactory;
use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class AbstractComposerJsonDecoratorTest extends AbstractKernelTestCase
{
    /**
     * @var ComposerJsonFactory
     */
    protected $composerJsonFactory;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->composerJsonFactory = self::$container->get(ComposerJsonFactory::class);
    }

    public static function dumpArrayToJsonFile(array $json, string $filePath): void
    {
        $content = Json::encode($json, Json::PRETTY);
        FileSystem::write($filePath, $content);
    }

    /**
     * @param mixed[]|SmartFileInfo|string $source
     */
    protected function createComposerJson($source): ComposerJson
    {
        if (is_array($source)) {
            return $this->composerJsonFactory->createFromArray($source);
        }

        if ($source instanceof SmartFileInfo) {
            return $this->composerJsonFactory->createFromFileInfo($source);
        }

        return $this->composerJsonFactory->createFromFilePath($source);
    }

    /**
     * @param string|ComposerJson $firstComposerJson
     */
    protected function assertComposerJsonEquals($firstComposerJson, ComposerJson $secondComposerJson): void
    {
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
    }
}
