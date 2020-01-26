<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Symplify\MonorepoBuilder\ComposerJsonObject\ComposerJsonFactory;
use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

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
     * @param mixed[]|string $source
     */
    protected function createComposerJson($source): ComposerJson
    {
        if (is_array($source)) {
            return $this->composerJsonFactory->createFromArray($source);
        }

        return $this->composerJsonFactory->createFromFilePath($source);
    }

    protected function assertComposerJsonEquals(ComposerJson $firstComposerJson, ComposerJson $secondComposerJson): void
    {
        $this->assertSame($firstComposerJson->getAutoload(), $secondComposerJson->getAutoload());
        $this->assertSame($firstComposerJson->getAutoloadDev(), $secondComposerJson->getAutoloadDev());

        $this->assertSame($firstComposerJson->getRequire(), $secondComposerJson->getRequire());
        $this->assertSame($firstComposerJson->getRequireDev(), $secondComposerJson->getRequireDev());

        $this->assertSame($firstComposerJson->getName(), $secondComposerJson->getName());
        $this->assertSame($firstComposerJson->getRepositories(), $secondComposerJson->getRepositories());

        $this->assertSame($firstComposerJson->getReplace(), $secondComposerJson->getReplace());
        $this->assertSame($firstComposerJson->getExtra(), $secondComposerJson->getExtra());
    }
}
