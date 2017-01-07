<?php

namespace Symplify\PHP7_CodeSniffer\Tests\File\Finder;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\File\Finder\SourceFinder;

final class SourceFinderTest extends TestCase
{
    /**
     * @var SourceFinder
     */
    private $sourceFinder;

    protected function setUp()
    {
        $this->sourceFinder = new SourceFinder();
    }

    /**
     * @dataProvider provideFindData()
     */
    public function testFind(array $source, int $numberOfFoundFiles)
    {
        $this->assertCount(
            $numberOfFoundFiles,
            $this->sourceFinder->find($source)
        );
    }

    public function provideFindData() : array
    {
        return [
            [
                [__DIR__], 2
            ], [
                [__DIR__.'/SourceFinderSource'], 1
            ], [
                [__DIR__.'/SourceFinderSource/SomeFiles/SomeSource.php'], 1
            ], [
                [__DIR__.'/SourceFinderSource/SomeFiles/SomeSource.txt'], 0
            ]
        ];
    }
}
