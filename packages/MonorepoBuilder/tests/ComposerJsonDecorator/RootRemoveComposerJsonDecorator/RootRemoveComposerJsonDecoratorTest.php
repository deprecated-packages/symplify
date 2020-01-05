<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator\RootRemoveComposerJsonDecorator;

use Symplify\MonorepoBuilder\DependenciesMerger;
use Symplify\MonorepoBuilder\FileSystem\JsonFileManager;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

/**
 * @see \Symplify\MonorepoBuilder\ComposerJsonDecorator\RootRemoveComposerJsonDecorator
 */
final class RootRemoveComposerJsonDecoratorTest extends AbstractKernelTestCase
{
    /**
     * @var JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var DependenciesMerger
     */
    private $dependenciesMerger;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->jsonFileManager = self::$container->get(JsonFileManager::class);
        $this->dependenciesMerger = self::$container->get(DependenciesMerger::class);
    }

    /**
     * Only packages collected from /packages directory should be removed
     */
    public function test(): void
    {
        $jsonToMerge = $this->jsonFileManager->loadFromFilePath(__DIR__ . '/Source/packages/composer.json');

        $resultJson = $this->dependenciesMerger->mergeJsonToRootFilePath(
            $jsonToMerge,
            __DIR__ . '/Source/composer.json'
        );
        $expectedRootJson = $this->jsonFileManager->loadFromFilePath(__DIR__ . '/Source/expected-composer.json');

        $this->assertSame($expectedRootJson, $resultJson);
    }
}
