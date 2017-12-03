<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Source;

use Nette\Utils\Finder;
use PHPUnit\Framework\TestCase;
use Symplify\Statie\Source\SourceFileFilter\GlobalLatteSourceFilter;
use Symplify\Statie\Source\SourceFileFilter\RenderableSourceFilter;
use Symplify\Statie\Source\SourceFileStorage;

final class SourceFileStorageTest extends TestCase
{
    public function test(): void
    {
        $sourceFileStorage = $this->prepareSourceFileStorage();

        $this->assertCount(1, $sourceFileStorage->getRenderableFiles());
    }

    private function prepareSourceFileStorage(): SourceFileStorage
    {
        $sourceFileStorage = new SourceFileStorage();

        $sourceFileStorage->addSourceFileFilter(new GlobalLatteSourceFilter());
        $sourceFileStorage->addSourceFileFilter(new RenderableSourceFilter());

        $finder = Finder::findFiles('*')->from(__DIR__ . '/SourceFileStorageSource');
        $sourceFileStorage->loadSourcesFromFiles(iterator_to_array($finder));

        return $sourceFileStorage;
    }
}
