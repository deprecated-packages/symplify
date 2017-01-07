<?php

namespace Symplify\PHP7_CodeSniffer\Tests\File\Provider;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\File\File;
use Symplify\PHP7_CodeSniffer\File\Finder\SourceFinder;
use Symplify\PHP7_CodeSniffer\File\Provider\FilesProvider;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class FilesProviderTest extends TestCase
{
    /**
     * @var FilesProvider
     */
    private $filesProvider;

    protected function setUp()
    {
        $this->filesProvider = new FilesProvider(
            new SourceFinder(),
            Instantiator::createFileFactory()
        );
    }

    public function test()
    {
        $source = [__DIR__.'/FilesProviderSource'];
        $files = $this->filesProvider->getFilesForSource($source, false);
        $this->assertCount(1, $files);

        $file = array_pop($files);
        $this->assertInstanceOf(File::class, $file);
    }
}
