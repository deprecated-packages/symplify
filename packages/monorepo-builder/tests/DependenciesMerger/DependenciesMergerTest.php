<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\DependenciesMerger;

use Symplify\MonorepoBuilder\ComposerJsonMerger;
use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Tests\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;

final class DependenciesMergerTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var ComposerJsonMerger
     */
    private $composerJsonMerger;

    /**
     * @var ComposerJson
     */
    private $composerJson;

    /**
     * @var ComposerJson
     */
    private $mergedComposerJson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->composerJson = $this->createComposerJson(__DIR__ . '/Source/main-composer.json');
        $this->composerJsonMerger = self::$container->get(ComposerJsonMerger::class);

        $this->mergedComposerJson = $this->createComposerJson(__DIR__ . '/Source/merged-composer.json');
    }

    public function test(): void
    {
        $this->composerJsonMerger->mergeJsonToRoot($this->mergedComposerJson, $this->composerJson);

        $expectedComposerJson = $this->createComposerJson(__DIR__ . '/Source/expected-root.json');

        $this->assertComposerJsonEquals($expectedComposerJson, $this->composerJson);
    }
}
