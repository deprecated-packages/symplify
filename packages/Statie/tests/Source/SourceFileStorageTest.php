<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Source;

use Nette\Utils\Finder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\Statie\Source\SourceFileFilter\GlobalLatteSourceFilter;
use Symplify\Statie\Source\SourceFileFilter\PostSourceFilter;
use Symplify\Statie\Source\SourceFileFilter\RenderableSourceFilter;
use Symplify\Statie\Source\SourceFileFilter\StaticSourceFilter;
use Symplify\Statie\Source\SourceFileStorage;

final class SourceFileStorageTest extends TestCase
{
    public function test(): void
    {
        $sourceFileStorage = $this->prepareSourceFileStorage();

        $this->assertCount(1, $sourceFileStorage->getLayoutFiles());
        $this->assertCount(2, $sourceFileStorage->getStaticFiles());
        $this->assertCount(1, $sourceFileStorage->getRenderableFiles());
    }

    public function testCnameIsFound(): void
    {
        $sourceFileStorage = $this->prepareSourceFileStorage();

        $staticFiles = $sourceFileStorage->getStaticFiles();

        /** @var SplFileInfo $cnameStdFile */
        $cnameStdFile = array_shift($staticFiles);
        $this->assertStringEndsWith('CNAME', $cnameStdFile->getFilename());
    }

    private function prepareSourceFileStorage(): SourceFileStorage
    {
        $sourceFileStorage = new SourceFileStorage();

        $sourceFileStorage->addSourceFileFilter(new GlobalLatteSourceFilter());
        $sourceFileStorage->addSourceFileFilter(new StaticSourceFilter());
        $sourceFileStorage->addSourceFileFilter(new RenderableSourceFilter());

        $finder = Finder::findFiles('*')->from(__DIR__ . '/SourceFileStorageSource');
        $sourceFileStorage->loadSourcesFromFiles(iterator_to_array($finder));

        return $sourceFileStorage;
    }
}
