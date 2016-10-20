<?php

namespace Symplify\PHP7_Sculpin\Tests\Source;

use Nette\Utils\Finder;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_Sculpin\Source\SourceFileFilter\ConfigurationSourceFilter;
use Symplify\PHP7_Sculpin\Source\SourceFileFilter\LayoutSourceFilter;
use Symplify\PHP7_Sculpin\Source\SourceFileFilter\PostSourceFilter;
use Symplify\PHP7_Sculpin\Source\SourceFileFilter\RenderableSourceFilter;
use Symplify\PHP7_Sculpin\Source\SourceFileFilter\StaticSourceFilter;
use Symplify\PHP7_Sculpin\Source\SourceFileStorage;

final class SourceFileStorageTest extends TestCase
{
    public function test()
    {
        $sourceFileStorage = $this->prepareSourceFileStorage();

        $this->assertCount(1, $sourceFileStorage->getLayoutFiles());
        $this->assertCount(1, $sourceFileStorage->getPostFiles());
        $this->assertCount(1, $sourceFileStorage->getConfigurationFiles());
        $this->assertCount(1, $sourceFileStorage->getStaticFiles());
        $this->assertCount(1, $sourceFileStorage->getRenderableFiles());
    }

    private function prepareSourceFileStorage() : SourceFileStorage
    {
        $sourceFileStorage = new SourceFileStorage();

        $sourceFileStorage->addSourceFileFilter(new LayoutSourceFilter());
        $sourceFileStorage->addSourceFileFilter(new PostSourceFilter());
        $sourceFileStorage->addSourceFileFilter(new ConfigurationSourceFilter());
        $sourceFileStorage->addSourceFileFilter(new StaticSourceFilter());
        $sourceFileStorage->addSourceFileFilter(new RenderableSourceFilter());

        $finder = Finder::findFiles('*')->from(__DIR__.'/SculpinFileStorageSource');
        $sourceFileStorage->loadSourcesFromFinder($finder);

        return $sourceFileStorage;
    }
}
