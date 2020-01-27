<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonMerger;

use Symplify\MonorepoBuilder\Merge\ComposerJsonMerger;
use Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;

final class ComposerJsonMergerTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var ComposerJsonMerger
     */
    private $composerJsonMerger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->composerJsonMerger = self::$container->get(ComposerJsonMerger::class);
    }

    public function test(): void
    {
        $mainComposerJson = $this->createComposerJson(__DIR__ . '/Source/main-composer.json');
        $mergedComposerJson = $this->createComposerJson(__DIR__ . '/Source/merged-composer.json');

        $this->composerJsonMerger->mergeJsonToRoot($mainComposerJson, $mergedComposerJson);

        $this->assertComposerJsonEquals(__DIR__ . '/Source/expected-root.json', $mainComposerJson);
    }
}
