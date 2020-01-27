<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonMerger;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\ComposerJsonMerger;
use Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;

final class ComposerJsonMergerTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var ComposerJsonMerger
     */
    private $composerJsonMerger;

    /**
     * @var ComposerJson
     */
    private $mainComposerJson;

    /**
     * @var ComposerJson
     */
    private $mergedComposerJson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mainComposerJson = $this->createComposerJson(__DIR__ . '/Source/main-composer.json');
        $this->mergedComposerJson = $this->createComposerJson(__DIR__ . '/Source/merged-composer.json');

        $this->composerJsonMerger = self::$container->get(ComposerJsonMerger::class);
    }

    public function test(): void
    {
        $this->composerJsonMerger->mergeJsonToRoot($this->mainComposerJson, $this->mergedComposerJson);

        $this->assertComposerJsonEquals(__DIR__ . '/Source/expected-root.json', $this->mainComposerJson);
    }
}
