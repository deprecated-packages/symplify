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

        $this->composerJsonMerger = $this->getService(ComposerJsonMerger::class);
    }

    public function test(): void
    {
        $mainComposerJson = $this->createComposerJson(__DIR__ . '/Source/main-composer.json');
        $package1Json = $this->createComposerJson(__DIR__ . '/Source/package1-composer.json');
        $package2Json = $this->createComposerJson(__DIR__ . '/Source/package2-composer.json');

        $this->composerJsonMerger->mergeJsonToRoot($mainComposerJson, $package1Json);
        $this->composerJsonMerger->mergeJsonToRoot($mainComposerJson, $package2Json);

        $this->assertComposerJsonEquals(__DIR__ . '/Source/expected-root.json', $mainComposerJson);
    }
}
