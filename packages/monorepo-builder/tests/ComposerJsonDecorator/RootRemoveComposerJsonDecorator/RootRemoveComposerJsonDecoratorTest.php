<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator\RootRemoveComposerJsonDecorator;

use Symplify\MonorepoBuilder\ComposerJsonMerger;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;

/**
 * @see \Symplify\MonorepoBuilder\ComposerJsonDecorator\RootRemoveComposerJsonDecorator
 */
final class RootRemoveComposerJsonDecoratorTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var ComposerJsonMerger
     */
    private $composerJsonMerger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bootKernel(MonorepoBuilderKernel::class);
        $this->composerJsonMerger = self::$container->get(ComposerJsonMerger::class);
    }

    /**
     * Only packages collected from /packages directory should be removed
     */
    public function test(): void
    {
        $composerJson = $this->composerJsonFactory->createFromFilePath(__DIR__ . '/Source/composer.json');
        $extraComposerJson = $this->composerJsonFactory->createFromFilePath(__DIR__ . '/Source/packages/composer.json');

        $this->composerJsonMerger->mergeJsonToRoot($extraComposerJson, $composerJson);

        $expectedComposerJson = $this->composerJsonFactory->createFromFilePath(
            __DIR__ . '/Source/expected-composer.json'
        );

        $this->assertComposerJsonEquals($expectedComposerJson, $composerJson);
    }
}
