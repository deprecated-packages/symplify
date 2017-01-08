<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Source;

use Nette\Utils\Finder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\Statie\Source\SourceFileFilter\ConfigurationSourceFilter;
use Symplify\Statie\Source\SourceFileFilter\GlobalLatteSourceFilter;
use Symplify\Statie\Source\SourceFileFilter\PostSourceFilter;
use Symplify\Statie\Source\SourceFileFilter\RenderableSourceFilter;
use Symplify\Statie\Source\SourceFileFilter\StaticSourceFilter;
use Symplify\Statie\Source\SourceFileStorage;

final class SourceFileStorageTest extends TestCase
{
    public function test()
    {
        $sourceFileStorage = $this->prepareSourceFileStorage();

        $this->assertCount(1, $sourceFileStorage->getLayoutFiles());
        $this->assertCount(1, $sourceFileStorage->getConfigurationFiles());
        $this->assertCount(2, $sourceFileStorage->getStaticFiles());
        $this->assertCount(1, $sourceFileStorage->getRenderableFiles());
    }

    public function testPostDescendentSorting()
    {
        $sourceFileStorage = $this->prepareSourceFileStorage();

        $postFiles = $sourceFileStorage->getPostFiles();
        /** @var SplFileInfo $firstPost */
        $firstPost = array_shift($postFiles);
        /** @var SplFileInfo $secondPost */
        $secondPost = array_shift($postFiles);

        $this->assertCount(3, $sourceFileStorage->getPostFiles());

        $this->assertSame('2016-01-30-post.latte', $firstPost->getFilename());
        $this->assertSame('2016-05-10-post.latte', $secondPost->getFilename());
    }

    public function testCnameIsFound()
    {
        $sourceFileStorage = $this->prepareSourceFileStorage();

        $staticFiles = $sourceFileStorage->getStaticFiles();

        /** @var SplFileInfo $cnameStdFile */
        $cnameStdFile = array_shift($staticFiles);
        $this->assertStringEndsWith('CNAME', $cnameStdFile->getFilename());
    }

    private function prepareSourceFileStorage() : SourceFileStorage
    {
        $sourceFileStorage = new SourceFileStorage();

        $sourceFileStorage->addSourceFileFilter(new GlobalLatteSourceFilter());
        $sourceFileStorage->addSourceFileFilter(new PostSourceFilter());
        $sourceFileStorage->addSourceFileFilter(new ConfigurationSourceFilter());
        $sourceFileStorage->addSourceFileFilter(new StaticSourceFilter());
        $sourceFileStorage->addSourceFileFilter(new RenderableSourceFilter());

        $finder = Finder::findFiles('*')->from(__DIR__ . '/SourceFileStorageSource');
        $sourceFileStorage->loadSourcesFromFiles(iterator_to_array($finder));

        return $sourceFileStorage;
    }
}
